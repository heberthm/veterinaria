<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role; // Si usas Spatie para roles

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Opcional: Si usas roles, puedes proteger el controlador aquí
        // $this->middleware('permission:ver-usuarios')->only('index');
    }

    /**
     * Listar todos los usuarios
     */
    public function index()
    {
        $users = User::where('tenant_id', auth()->user()->tenant_id ?? null)
                     ->orderBy('id', 'desc')
                     ->paginate(10);
                     
        return view('users.index', compact('users'));
    }

    /**
     * Mostrar formulario para crear un usuario
     */
    public function create()
    {
        // Si usas Spatie, pasamos los roles a la vista
        $roles = class_exists('Spatie\Permission\Models\Role') ? Role::all() : [];
        return view('users.create', compact('roles'));
    }

    /**
     * Guardar un nuevo usuario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'nullable|string', // Si usas campo directo
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'rol' => $request->rol ?? 'veterinarian', // Ajusta según tu lógica
        ]);

        // Si usas Spatie Laravel Permission:
        if ($request->has('roles') && class_exists('Spatie\Permission\Models\Role')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar un usuario
     */
    public function edit(User $user)
    {
        $roles = class_exists('Spatie\Permission\Models\Role') ? Role::all() : [];
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('rol')) {
            $user->rol = $request->rol;
        }
        
        $user->save();

        if ($request->has('roles') && class_exists('Spatie\Permission\Models\Role')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(User $user)
    {
        // Evitar que el usuario se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}