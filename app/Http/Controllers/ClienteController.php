<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function datatable(Request $request)
    {
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null);

        return DataTables::of($clientes)
            ->addColumn('documento', function($cliente) {
                return $cliente->tipo_documento . ' - ' . $cliente->numero_documento;
            })
            ->addColumn('mascotas_count', function($cliente) {
                return $cliente->mascotas()->count();
            })
            ->addColumn('estado', function($cliente) {
                return $cliente->activo 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>';
            })
            ->addColumn('acciones', function($cliente) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verCliente(' . $cliente->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-accion" onclick="editarCliente(' . $cliente->id . ')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-accion" onclick="eliminarCliente(' . $cliente->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|in:CC,CE,NIT,PA,RC',
            'numero_documento' => 'required|string|max:20|unique:clientes,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'nullable|email|unique:clientes,email',
            // 'telefono' => 'nullable|string|max:20',  // ❌ ELIMINADO
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'barrio' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'nullable|in:masculino,femenino,otro',
            'activo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cliente = Cliente::create([
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            // 'telefono' => $request->telefono,  // ❌ ELIMINADO
            'celular' => $request->celular,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'barrio' => $request->barrio,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'activo' => $request->has('activo'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente registrado exitosamente',
            'cliente' => $cliente
        ]);
    }

    public function detalle($id)
    {
        $cliente = Cliente::with(['mascotas', 'citas'])
            ->findOrFail($id);

        return view('modales.cliente-detalle', compact('cliente'));
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes-editar', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|in:CC,CE,NIT,PA,RC',
            'numero_documento' => 'required|string|max:20|unique:clientes,numero_documento,' . $id,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'nullable|email|unique:clientes,email,' . $id,
            // 'telefono' => 'nullable|string|max:20',  // ❌ ELIMINADO
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'barrio' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'nullable|in:masculino,femenino,otro',
            'activo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cliente->update([
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            // 'telefono' => $request->telefono,  // ❌ ELIMINADO
            'celular' => $request->celular,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'barrio' => $request->barrio,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'activo' => $request->has('activo'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente'
        ]);
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->mascotas()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el cliente porque tiene mascotas asociadas.'
            ], 422);
        }

        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente'
        ]);
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q', '');
        
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where(function($q) use ($query) {
                $q->where('nombres', 'LIKE', "%{$query}%")
                  ->orWhere('apellidos', 'LIKE', "%{$query}%")
                  ->orWhere('numero_documento', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('celular', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json($clientes->map(function($cliente) {
            return [
                'id' => $cliente->id,
                'text' => $cliente->nombres . ' ' . $cliente->apellidos . ' - ' . $cliente->numero_documento,
                'nombres' => $cliente->nombres,
                'apellidos' => $cliente->apellidos,
                'numero_documento' => $cliente->numero_documento,
                'celular' => $cliente->celular,
                'email' => $cliente->email,
            ];
        }));
    }
}