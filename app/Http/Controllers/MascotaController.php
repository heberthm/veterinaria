<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MascotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de mascotas
     */
    public function index()
    {
        // 🔥 OBTENER CLIENTES PARA EL SELECT DEL MODAL
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('activo', true)
            ->get();

        // 🔥 PASAR $clientes A LA VISTA
        return view('mascotas', compact('clientes'));
    }

    /**
     * DataTable para mascotas
     */
    public function datatable(Request $request)
    {
        $mascotas = Mascota::with(['cliente'])
            ->whereHas('cliente', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            });

        return DataTables::of($mascotas)
            ->addColumn('cliente_nombre', function($mascota) {
                return $mascota->cliente->nombres . ' ' . $mascota->cliente->apellidos;
            })
            ->addColumn('especie_label', function($mascota) {
                $especies = [
                    'perro' => '🐕 Perro',
                    'gato' => '🐈 Gato',
                    'ave' => '🐦 Ave',
                    'roedor' => '🐹 Roedor',
                    'reptil' => '🦎 Reptil',
                    'equino' => '🐴 Equino',
                    'bovino' => '🐄 Bovino',
                    'porcino' => '🐷 Porcino',
                    'conejo' => '🐰 Conejo',
                ];
                return $especies[$mascota->especie] ?? $mascota->especie;
            })
            ->addColumn('genero_label', function($mascota) {
                return $mascota->genero == 'macho' ? '♂️ Macho' : '♀️ Hembra';
            })
            ->addColumn('edad', function($mascota) {
                return $mascota->edad . ' años';
            })
            ->addColumn('estado', function($mascota) {
                return $mascota->activo 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>';
            })
            ->addColumn('acciones', function($mascota) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verMascota(' . $mascota->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-accion" onclick="editarMascota(' . $mascota->id . ')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-accion" onclick="eliminarMascota(' . $mascota->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    /**
     * Guardar nueva mascota
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:100',
            'especie' => 'required|in:perro,gato,ave,roedor,reptil,equino,bovino,porcino,conejo',
            'raza' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'required|in:macho,hembra',
            'peso' => 'nullable|numeric|min:0|max:200',
            'numero_chip' => 'nullable|string|max:50|unique:mascotas,numero_chip',
            'activo' => 'nullable|boolean',
            'esterilizado' => 'nullable|boolean',
            'alergias' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mascota = Mascota::create([
            'cliente_id' => $request->cliente_id,
            'nombre' => $request->nombre,
            'especie' => $request->especie,
            'raza' => $request->raza,
            'color' => $request->color,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'peso' => $request->peso,
            'numero_chip' => $request->numero_chip,
            'activo' => $request->has('activo'),
            'esterilizado' => $request->has('esterilizado'),
            'alergias' => $request->alergias,
            'enfermedades_cronicas' => $request->enfermedades_cronicas,
            'notas' => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota registrada exitosamente',
            'mascota' => $mascota
        ]);
    }

    /**
     * Mostrar detalle de mascota (modal)
     */
    public function detalle($id)
    {
        $mascota = Mascota::with(['cliente', 'citas', 'historiasClinicas'])
            ->findOrFail($id);

        return view('modales.mascota-detalle', compact('mascota'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $mascota = Mascota::findOrFail($id);
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('activo', true)
            ->get();

        return view('mascotas-editar', compact('mascota', 'clientes'));
    }

    /**
     * Actualizar mascota
     */
    public function update(Request $request, $id)
    {
        $mascota = Mascota::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'nombre' => 'required|string|max:100',
            'especie' => 'required|in:perro,gato,ave,roedor,reptil,equino,bovino,porcino,conejo',
            'raza' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'required|in:macho,hembra',
            'peso' => 'nullable|numeric|min:0|max:200',
            'numero_chip' => 'nullable|string|max:50|unique:mascotas,numero_chip,' . $id,
            'activo' => 'nullable|boolean',
            'esterilizado' => 'nullable|boolean',
            'alergias' => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mascota->update([
            'cliente_id' => $request->cliente_id,
            'nombre' => $request->nombre,
            'especie' => $request->especie,
            'raza' => $request->raza,
            'color' => $request->color,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'peso' => $request->peso,
            'numero_chip' => $request->numero_chip,
            'activo' => $request->has('activo'),
            'esterilizado' => $request->has('esterilizado'),
            'alergias' => $request->alergias,
            'enfermedades_cronicas' => $request->enfermedades_cronicas,
            'notas' => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada exitosamente'
        ]);
    }

    /**
     * Eliminar mascota
     */
    public function destroy($id)
    {
        $mascota = Mascota::findOrFail($id);

        if ($mascota->citas()->whereIn('estado', ['agendada', 'confirmada', 'en_curso'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la mascota porque tiene citas activas.'
            ], 422);
        }

        $mascota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mascota eliminada exitosamente'
        ]);
    }

    /**
     * Obtener mascotas de un cliente (API)
     */
    public function getMascotasByCliente($clienteId)
    {
        $mascotas = Mascota::where('cliente_id', $clienteId)
            ->where('activo', true)
            ->select('id', 'nombre', 'especie', 'raza')
            ->get();

        return response()->json($mascotas);
    }

    /**
     * Buscar mascotas (API)
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q', '');
        
        $mascotas = Mascota::whereHas('cliente', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            })
            ->where(function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('numero_chip', 'LIKE', "%{$query}%")
                  ->orWhere('raza', 'LIKE', "%{$query}%");
            })
            ->with('cliente')
            ->limit(20)
            ->get();

        return response()->json($mascotas->map(function($mascota) {
            return [
                'id' => $mascota->id,
                'nombre' => $mascota->nombre,
                'especie' => $mascota->especie,
                'raza' => $mascota->raza,
                'cliente' => $mascota->cliente->nombres . ' ' . $mascota->cliente->apellidos,
                'text' => $mascota->nombre . ' - ' . $mascota->especie . ' (' . $mascota->cliente->nombres . ')'
            ];
        }));
    }
}