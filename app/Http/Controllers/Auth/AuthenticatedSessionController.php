<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $user = Auth::user();

        // El tenant ya no se determina por subdominio: se resuelve por
        // el usuario mismo (ver App\Http\Middleware\IdentifyTenant).
        // Aquí solo validamos que, si pertenece a una clínica, esa
        // clínica exista y no esté suspendida.
        if (!$user->esSuperAdmin()) {
            $tenant = $user->tenant;

            if (!$tenant) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Tu usuario no está asociado a ninguna clínica activa.',
                ]);
            }

            if ($tenant->estado === 'suspendido') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'La cuenta de tu clínica se encuentra suspendida. Contacta a soporte.',
                ]);
            }
        }

        if (!$user->activo) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu usuario se encuentra inactivo. Contacta al administrador.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
