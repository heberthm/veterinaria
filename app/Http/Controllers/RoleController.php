<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permisos = Permission::all();

        // Agrupar permisos por categoría (usando el prefijo antes del guion bajo)
        $permisosAgrupados = $permisos->groupBy(function ($permiso) {
            return explode('_', $permiso->name)[1] ?? 'general';
        });

        return view('roles', compact('roles', 'permisos', 'permisosAgrupados'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $role = Role::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Rol creado exitosamente.',
            'role' => $role
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $role->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado exitosamente.',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Admin') {
            return response()->json(['success' => false, 'message' => 'El rol Administrador no se puede eliminar.'], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado exitosamente.'
        ]);
    }

    // Sincronizar permisos de un rol
    public function syncPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permisos = $request->input('permissions', []);

        $role->syncPermissions($permisos);

        return response()->json([
            'success' => true,
            'message' => 'Permisos actualizados correctamente.'
        ]);
    }

    // Obtener permisos actuales de un rol (para marcar los checkboxes)
    public function getPermisosActuales($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        return response()->json([
            'permissions' => $role->permissions->pluck('name')
        ]);
    }
}