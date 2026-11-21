<?php

namespace App\Http\Controllers;

use App\Models\Veterinario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VeterinarioController extends Controller
{
    public function index()
    {
        return view('veterinarios.index');
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

        $validator = Validator::make($request->all(), [
            'cedula' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'especialidad' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $veterinario = Veterinario::create([
            'tenant_id' => $tenantId,
            'cedula' => $request->cedula,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'celular' => $request->celular,
            'email' => $request->email,
            'especialidad' => $request->especialidad,
            'activo' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Veterinario creado exitosamente',
            'veterinario' => $veterinario
        ]);
    }

        public function buscar(Request $request)
    {
        $query = trim($request->get('q', ''));
        $tenantId = auth()->user()->tenant_id ?? 1;

        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        $veterinarios = \App\Models\Veterinario::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('apellidos', 'LIKE', "%{$query}%")
                  ->orWhere('cedula', 'LIKE', "%{$query}%");
            })
            ->limit(15)
            ->get();

        return response()->json([
            'results' => $veterinarios->map(function($v) {
                return [
                    'id' => $v->id,
                    'text' => "Dr. {$v->nombre} {$v->apellidos}" . ($v->especialidad ? " - {$v->especialidad}" : "")
                ];
            })
        ]);
    }

}