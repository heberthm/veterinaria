<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('activo', true)
            ->orderBy('nombres')
            ->get();

        return view('mascotas', compact('clientes'));
    }

    /**
     * DataTable para mascotas
     */
    public function datatable(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $mascotas = Mascota::with(['cliente'])
            ->whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

        return DataTables::of($mascotas)
            ->addColumn('cliente_nombre', function ($mascota) {
                if (!$mascota->cliente) return '—';
                return $mascota->cliente->nombres . ' ' . $mascota->cliente->apellidos;
            })
            ->addColumn('especie_label', function ($mascota) {
                $especies = [
                    'perro'   => '🐕 Perro',
                    'gato'    => '🐈 Gato',
                    'ave'     => '🐦 Ave',
                    'roedor'  => '🐹 Roedor',
                    'reptil'  => '🦎 Reptil',
                    'equino'  => '🐴 Equino',
                    'bovino'  => '🐄 Bovino',
                    'porcino' => '🐷 Porcino',
                    'conejo'  => '🐰 Conejo',
                ];
                return $especies[$mascota->especie] ?? $mascota->especie;
            })
            ->addColumn('genero_label', function ($mascota) {
                return $mascota->genero === 'macho' ? '♂️ Macho' : '♀️ Hembra';
            })
            ->addColumn('edad', function ($mascota) {
                return $mascota->edad;
            })
            ->addColumn('estado', function ($mascota) {
                // Normalizar por si hay valores viejos con mayúscula
                $estado = strtolower($mascota->estado ?? 'activo');

                $map = [
                    'activo'    => ['class' => 'badge-success',   'label' => '🟢 Activo'],
                    'inactivo'  => ['class' => 'badge-secondary', 'label' => '⚪ Inactivo'],
                    'fallecido' => ['class' => 'badge-dark',      'label' => '⚫ Fallecido'],
                ];

                $e = $map[$estado] ?? ['class' => 'badge-secondary', 'label' => $estado];

                return '<span class="badge ' . $e['class'] . '">' . $e['label'] . '</span>';
            })
            ->addColumn('acciones', function ($m) {
                return '
                    <div class="col-acciones">
                        <button class="btn-accion btn-accion-ver"      onclick="verMascota('     . $m->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn-accion btn-accion-editar"   onclick="editarMascota('  . $m->id . ')" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn-accion btn-accion-eliminar" onclick="eliminarMascota(' . $m->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
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
        $tenantId = auth()->user()->tenant_id;

        $validator = Validator::make($request->all(), [
            'cliente_id'            => 'required|exists:clientes,id',
            'nombre'                => 'required|string|max:100',
            'especie'               => 'required|in:perro,gato,ave,roedor,reptil,equino,bovino,porcino,conejo',
            'raza'                  => 'nullable|string|max:100',
            'color'                 => 'nullable|string|max:50',
            'fecha_nacimiento'      => 'nullable|date|before:today',
            'genero'                => 'required|in:macho,hembra',
            'peso'                  => 'nullable|numeric|min:0|max:200',
            'numero_chip'           => [
                'nullable', 'string', 'max:50',
                Rule::unique('mascotas', 'numero_chip')->where('tenant_id', $tenantId),
            ],
            'estado'                => 'required|in:activo,inactivo,fallecido',
            'esterilizado'          => 'nullable|boolean',
            'alergias'              => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'notas'                 => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 🔥 Heredar tenant_id del cliente
        $cliente = Cliente::findOrFail($request->cliente_id);

        $estado = strtolower($request->estado ?? 'activo');

        $mascota = Mascota::create([
            'tenant_id'             => $cliente->tenant_id,
            'cliente_id'            => $request->cliente_id,
            'nombre'                => $request->nombre,
            'especie'               => $request->especie,
            'raza'                  => $request->raza,
            'color'                 => $request->color,
            'fecha_nacimiento'      => $request->fecha_nacimiento,
            'genero'                => $request->genero,
            'peso'                  => $request->peso,
            'numero_chip'           => $request->numero_chip,
            'estado'                => $estado,
            'esterilizado'          => $request->boolean('esterilizado'),
            'alergias'              => $request->alergias,
            'enfermedades_cronicas' => $request->enfermedades_cronicas,
            'notas'                 => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota registrada exitosamente',
            'mascota' => $mascota,
        ]);
    }

    /**
     * Detalle de mascota (JSON)
     */
    public function detalle($id)
    {
        $tenantId = auth()->user()->tenant_id;

        $mascota = Mascota::with(['cliente'])
            ->whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'mascota' => $mascota,
        ]);
    }

    /**
     * Formulario de edición (JSON)
     */
    public function edit($id)
    {
        $tenantId = auth()->user()->tenant_id;

        $mascota = Mascota::with(['cliente'])
            ->whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'mascota' => $mascota,
        ]);
    }

    /**
     * Actualizar mascota
     */
    public function update(Request $request, $id)
    {
         // 🔥 DEBUG TEMPORAL
    \Log::info('=== UPDATE MASCOTA DEBUG ===', [
        'id'         => $id,
        'estado_raw' => $request->estado,
        'estado_tipo'=> gettype($request->estado),
        'todos'      => $request->except(['_method', '_token']),
    ]);

        $tenantId = auth()->user()->tenant_id;

        $mascota = Mascota::whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'cliente_id'            => 'required|exists:clientes,id',
            'nombre'                => 'required|string|max:100',
            'especie'               => 'required|in:perro,gato,ave,roedor,reptil,equino,bovino,porcino,conejo',
            'raza'                  => 'nullable|string|max:100',
            'color'                 => 'nullable|string|max:50',
            'fecha_nacimiento'      => 'nullable|date|before:today',
            'genero'                => 'required|in:macho,hembra',
            'peso'                  => 'nullable|numeric|min:0|max:200',
            'numero_chip'           => [
                'nullable', 'string', 'max:50',
                Rule::unique('mascotas', 'numero_chip')
                    ->where('tenant_id', $tenantId)
                    ->ignore($id),
            ],
            'estado'                => 'required|in:activo,inactivo,fallecido',
            'esterilizado'          => 'nullable|boolean',
            'alergias'              => 'nullable|string',
            'enfermedades_cronicas' => 'nullable|string',
            'notas'                 => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        $estado  = strtolower($request->estado ?? 'activo');

        $mascota->update([
            'tenant_id'             => $cliente->tenant_id,
            'cliente_id'            => $request->cliente_id,
            'nombre'                => $request->nombre,
            'especie'               => $request->especie,
            'raza'                  => $request->raza,
            'color'                 => $request->color,
            'fecha_nacimiento'      => $request->fecha_nacimiento,
            'genero'                => $request->genero,
            'peso'                  => $request->peso,
            'numero_chip'           => $request->numero_chip,
            'estado'                => $estado,
            'esterilizado'          => $request->boolean('esterilizado'),
            'alergias'              => $request->alergias,
            'enfermedades_cronicas' => $request->enfermedades_cronicas,
            'notas'                 => $request->notas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada exitosamente',
            'mascota' => $mascota->fresh(),
        ]);
    }

    /**
     * Eliminar mascota
     */
    public function destroy($id)
    {
        $tenantId = auth()->user()->tenant_id;

        $mascota = Mascota::whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->findOrFail($id);

        // Bloquear si tiene citas activas
        if ($mascota->citas()
                ->whereIn('estado', ['agendada', 'confirmada', 'en_curso'])
                ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la mascota porque tiene citas activas.',
            ], 422);
        }

        $mascota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mascota eliminada exitosamente',
        ]);
    }

    /**
     * Obtener mascotas de un cliente (API)
     */
    public function getMascotasByCliente($clienteId)
    {
        $tenantId = auth()->user()->tenant_id;

        $mascotas = Mascota::where('cliente_id', $clienteId)
            ->where('estado', 'activo')                    // 👈 cambiado de activo → estado
            ->whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->select('id', 'nombre', 'especie', 'raza')
            ->get();

        return response()->json($mascotas);
    }

    /**
     * Buscar mascotas (API / typeahead)
     */
    public function buscar(Request $request)
    {
        $query    = $request->get('q', '');
        $tenantId = auth()->user()->tenant_id;

        $mascotas = Mascota::whereHas('cliente', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('numero_chip', 'LIKE', "%{$query}%")
                  ->orWhere('raza', 'LIKE', "%{$query}%");
            })
            ->with('cliente')
            ->limit(20)
            ->get();

        return response()->json($mascotas->map(function ($mascota) {
            $clienteNombre = $mascota->cliente
                ? $mascota->cliente->nombres . ' ' . $mascota->cliente->apellidos
                : '';

            return [
                'id'      => $mascota->id,
                'nombre'  => $mascota->nombre,
                'especie' => $mascota->especie,
                'raza'    => $mascota->raza,
                'cliente' => $clienteNombre,
                'texto'   => $mascota->nombre . ' - ' . $mascota->especie . ' (' . $clienteNombre . ')',
            ];
        }));
    }
}