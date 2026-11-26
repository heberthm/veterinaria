<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** Roles que se pueden asignar (coinciden con los de adminlte_desc() del modelo User). */
    public const ROLES = ['Admin', 'Veterinario', 'Recepcionista', 'Cajero'];

    public function __construct()
    {
        $this->middleware('auth');
        // Opcional: solo administradores
        // $this->middleware('role:Admin');
    }

    /* ------------------------------------------------------------------ */
    /*  Utilidades                                                          */
    /* ------------------------------------------------------------------ */

    /** Un usuario solo puede gestionar usuarios de su misma clínica. */
    private function autorizar(User $user): void
    {
        abort_unless(
            (int) $user->tenant_id === (int) (auth()->user()->tenant_id ?? 0),
            403,
            'No puedes gestionar usuarios de otra clínica.'
        );
    }

    private function reglas(?User $user = null): array
    {
        return [
            // Largos según la migración: name 60, email 180, rol 20
            'name'         => 'required|string|max:60',
            'email'        => ['required', 'string', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user?->id)],
            'password'     => $user ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'rol'          => ['required', Rule::in(self::ROLES)],
            'telefono'     => 'nullable|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'activo'       => 'nullable|boolean',
        ];
    }

    private function mensajes(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'name.max'           => 'El nombre no puede superar 60 caracteres.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no es válido.',
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'email.max'          => 'El correo no puede superar 180 caracteres.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'rol.required'       => 'Selecciona un rol.',
            'rol.in'             => 'El rol seleccionado no es válido.',
        ];
    }

    /** Asigna el rol de Spatie solo si ese rol existe. */
    private function sincronizarRol(User $user, string $rol): void
    {
        if (!class_exists(Role::class)) {
            return;
        }
        if (Role::where('name', $rol)->where('guard_name', 'web')->exists()) {
            $user->syncRoles([$rol]);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Acciones                                                            */
    /* ------------------------------------------------------------------ */

    /** Listado de usuarios de la clínica (con búsqueda y paginación). */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('rol', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios', [
            'users' => $users,
            'roles' => self::ROLES,
            'q'     => $q,
        ]);
    }

    /** El formulario vive en un modal de la vista usuarios. */
    public function create()
    {
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return redirect()->route('users.index');
    }

    /** Datos de un usuario en JSON (por si se necesitan desde JS). */
    public function show(User $user)
    {
        $this->autorizar($user);

        return response()->json($user->only([
            'id', 'name', 'email', 'rol', 'telefono', 'especialidad', 'activo', 'created_at',
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->reglas(), $this->mensajes());

        $user = new User();
        $user->fill([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'telefono'     => $data['telefono'] ?? null,
            'especialidad' => $data['especialidad'] ?? null,
        ]);
        $user->password  = Hash::make($data['password']);
        $user->tenant_id = auth()->user()->tenant_id;
        $user->rol       = $data['rol'];                       // asignado directo: no depende de $fillable
        $user->activo    = $request->boolean('activo', true);
        $user->save();

        $this->sincronizarRol($user, $data['rol']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente.']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, User $user)
    {
        $this->autorizar($user);

        $data = $request->validate($this->reglas($user), $this->mensajes());

        $user->name         = $data['name'];
        $user->email        = $data['email'];
        $user->telefono     = $data['telefono'] ?? null;
        $user->especialidad = $data['especialidad'] ?? null;

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        // Sobre uno mismo no se cambia rol ni estado (evita quedarse sin acceso)
        if (auth()->id() !== $user->id) {
            $user->rol    = $data['rol'];
            $user->activo = $request->boolean('activo');
        }

        $user->save();

        if (auth()->id() !== $user->id) {
            $this->sincronizarRol($user, $data['rol']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $this->autorizar($user);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        try {
            $user->delete();
        } catch (QueryException $e) {
            // El usuario ya tiene ventas, historias, etc. asociadas
            return redirect()->back()->with(
                'error',
                'No se puede eliminar: el usuario tiene registros asociados. Desactívalo en su lugar.'
            );
        }

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}