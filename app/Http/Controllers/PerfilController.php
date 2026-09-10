<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Constructor - requiere autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el perfil del usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Auth::user();
        return view('perfil', compact('user'));
    }

    /**
     * Muestra el formulario de edición
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Actualiza el perfil del usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Elimina la cuenta del usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}