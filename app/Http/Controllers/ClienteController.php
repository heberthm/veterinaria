<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('clientes');
    }

    // ============================================================
    // 🔥 DATATABLE
    // ============================================================
    public function datatable(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $clientes = Cliente::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId));

        return DataTables::of($clientes)
            ->addColumn('documento', fn($c) => $c->tipo_documento . ' - ' . $c->numero_documento)
            ->addColumn('mascotas_count', fn($c) => $c->mascotas()->count())
            ->addColumn('estado', fn($c) => $c->activo
                ? '<span class="badge-activo">Activo</span>'
                : '<span class="badge-inactivo">Inactivo</span>')
            ->addColumn('acciones', function ($c) {
                return '
                    <button class="btn-accion btn-accion-ver"      onclick="verCliente('     . $c->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                    <button class="btn-accion btn-accion-editar"   onclick="editarCliente('  . $c->id . ')" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn-accion btn-accion-eliminar" onclick="eliminarCliente(' . $c->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
                ';
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    // ============================================================
    // 🔥 CREAR
    // ============================================================
    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validator = Validator::make($request->all(), [
            'tipo_documento'   => 'required|in:CC,CE,NIT,PA,RC',
            'numero_documento' => [
                'required', 'string', 'max:20',
                Rule::unique('clientes', 'numero_documento')->where('tenant_id', $tenantId),
            ],
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'email'            => [
                'nullable', 'email',
                Rule::unique('clientes', 'email')->where('tenant_id', $tenantId),
            ],
            'celular'          => 'nullable|string|max:20',
            'direccion'        => 'nullable|string',
            'ciudad'           => 'nullable|string|max:100',
            'barrio'           => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero'           => 'nullable|in:masculino,femenino,otro',
            'activo'           => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cliente = Cliente::create([
            'tenant_id'        => $tenantId,
            'tipo_documento'   => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres'          => $request->nombres,
            'apellidos'        => $request->apellidos,
            'email'            => $request->email,
            'celular'          => $request->celular,
            'direccion'        => $request->direccion,
            'ciudad'           => $request->ciudad,
            'barrio'           => $request->barrio,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero'           => $request->genero,
            'activo'           => $request->boolean('activo'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente registrado exitosamente',
            'cliente' => $cliente,
        ]);
    }

    // ============================================================
    // 🔥 EDIT — devuelve JSON con datos del cliente
    // ============================================================
    public function edit($id)
    {
        $cliente = Cliente::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'cliente' => $cliente,
        ]);
    }

    // ============================================================
    // 🔥 DETALLE — devuelve JSON con cliente + relaciones
    // ============================================================
    public function detalle($id)
    {
        $cliente = Cliente::where('tenant_id', auth()->user()->tenant_id)
            ->with(['mascotas', 'citas'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'cliente' => $cliente,
        ]);
    }

    // ============================================================
    // 🔥 ACTUALIZAR
    // ============================================================
    public function update(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;

        $cliente = Cliente::where('tenant_id', $tenantId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_documento'   => 'required|in:CC,CE,NIT,PA,RC',
            'numero_documento' => [
                'required', 'string', 'max:20',
                Rule::unique('clientes', 'numero_documento')
                    ->where('tenant_id', $tenantId)
                    ->ignore($id),
            ],
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'email'            => [
                'nullable', 'email',
                Rule::unique('clientes', 'email')
                    ->where('tenant_id', $tenantId)
                    ->ignore($id),
            ],
            'celular'          => 'nullable|string|max:20',
            'direccion'        => 'nullable|string',
            'ciudad'           => 'nullable|string|max:100',
            'barrio'           => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero'           => 'nullable|in:masculino,femenino,otro',
            'activo'           => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cliente->update([
            'tipo_documento'   => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres'          => $request->nombres,
            'apellidos'        => $request->apellidos,
            'email'            => $request->email,
            'celular'          => $request->celular,
            'direccion'        => $request->direccion,
            'ciudad'           => $request->ciudad,
            'barrio'           => $request->barrio,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero'           => $request->genero,
            'activo'           => $request->boolean('activo'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente->fresh(),
        ]);
    }

    // ============================================================
    // 🔥 ELIMINAR
    // ============================================================
    public function destroy($id)
    {
        $cliente = Cliente::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        if ($cliente->mascotas()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el cliente porque tiene mascotas asociadas.',
            ], 422);
        }

        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente',
        ]);
    }

    // ============================================================
    // 🔥 BUSCAR (typeahead del POS)
    // ============================================================
    public function buscar(Request $request)
    {
        $query    = $request->get('q', '');
        $tenantId = auth()->user()->tenant_id;

        $clientes = Cliente::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('nombres', 'LIKE', "%{$query}%")
                  ->orWhere('apellidos', 'LIKE', "%{$query}%")
                  ->orWhere('numero_documento', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('celular', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json($clientes->map(fn($c) => [
            'id'    => $c->id,
            'texto' => $c->nombres . ' ' . $c->apellidos . ' - ' . $c->numero_documento,
        ]));
    }
}