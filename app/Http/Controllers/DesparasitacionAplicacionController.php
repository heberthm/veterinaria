<?php

namespace App\Http\Controllers;

use App\Models\DesparasitacionAplicacion;
use App\Models\Desparasitante;
use App\Models\Mascota;
use App\Models\Veterinario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DesparasitacionAplicacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de Desparasitaciones
     */
   public function index()
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

        // 1. Obtener las mascotas del tenant actual
        $mascotas = Mascota::whereHas('cliente', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->with('cliente')->get();

        // 🔥 2. Obtener los veterinarios desde la nueva tabla
        $veterinarios = Veterinario::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $desparasitantes = Desparasitante::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('Desparasitaciones', compact('mascotas', 'veterinarios', 'desparasitantes'));
     
    }


    /**
     * DataTable para Desparasitaciones
     */
       public function datatable(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

        // 🔥 CONSULTA DIRECTA CON JOINS
        $consulta = \Illuminate\Support\Facades\DB::table('desparasitacion_aplicaciones as a')
            ->join('mascotas as m', 'm.id', '=', 'a.mascota_id')
            ->join('clientes as c', 'c.id', '=', 'm.cliente_id')
            ->join('desparasitantes as d', 'd.id', '=', 'a.desparasitante_id') // Usamos JOIN
            ->leftJoin('users as u', 'u.id', '=', 'a.usuario_id')
            ->where('c.tenant_id', $tenantId)
            ->select([
                'a.id', 
                'a.fecha_aplicacion', 
                'a.fecha_proxima', 
                'a.lote', 
                'a.estado',
                'm.nombre as mascota_nombre',
                \Illuminate\Support\Facades\DB::raw("TRIM(CONCAT(c.nombres, ' ', COALESCE(c.apellidos, ''))) as cliente_nombre"),
                'd.nombre as producto',
                'd.tipo as tipo', // Traemos el tipo del desparasitante
                'u.name as veterinario_nombre'
            ]);

        $fecha = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d/m/Y') : '—';

        return DataTables::of($consulta)
            ->editColumn('veterinario_nombre', fn ($r) => $r->veterinario_nombre ? 'Dr. ' . $r->veterinario_nombre : 'N/A')
            ->addColumn('fecha_aplicacion_formato', fn ($r) => $fecha($r->fecha_aplicacion))
            ->addColumn('fecha_proxima_formato', fn ($r) => $fecha($r->fecha_proxima))
            ->addColumn('estado_badge', function ($r) {
                $color = ['aplicada' => 'success', 'programada' => 'warning', 'cancelada' => 'secondary'][$r->estado] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . e(ucfirst((string) $r->estado)) . '</span>';
            })
            ->addColumn('tipo_label', function ($r) {
                return ucfirst((string) $r->tipo); // "Interna", "Externa", "Mixta"
            })
            ->addColumn('acciones', function ($r) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="verDesparasitacion(' . $r->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editarDesparasitacion(' . $r->id . ')" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="eliminarDesparasitacion(' . $r->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

    /**
     * 🔥 MOSTRAR FORMULARIO DE CREACIÓN (MODAL)
     */
    public function create(Request $request)
    {
        try {
            $mascotaId = $request->get('mascota_id', null);
            $mascota = $mascotaId ? Mascota::with('cliente')->find($mascotaId) : null;

            $mascotas = Mascota::whereHas('cliente', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            })->with('cliente')->get();

            $veterinarios = User::whereHas('roles', function($q) {
                $q->where('name', 'veterinarian');
            })->get();

            if ($veterinarios->isEmpty()) {
                $veterinarios = User::where('rol', 'veterinarian')
                    ->orWhere('rol', 'Veterinario')
                    ->get();
            }

            return view('modales.DesparasitacionAplicacion-form', compact('mascotas', 'veterinarios', 'mascota'));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Guardar nueva desparasitación
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascota_id'        => 'required|exists:mascotas,id',
            'usuario_id'        => 'required|exists:veterinarios,id', 
            'desparasitante_id' => 'nullable|exists:desparasitantes,id', 
            'fecha_aplicacion'  => 'required|date',
            'fecha_proxima'     => 'nullable|date|after:fecha_aplicacion',
            'dosis_aplicada'    => 'nullable|numeric|min:0',
            'lote'              => 'nullable|string|max:50',
            'via_aplicacion'    => 'nullable|string|max:50',
            'costo'             => 'nullable|numeric|min:0',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $aplicacion = DesparasitacionAplicacion::create([
            'mascota_id'        => $request->mascota_id,
            'usuario_id'        => $request->usuario_id,
            'desparasitante_id' => $request->desparasitante_id,
            'fecha_aplicacion'  => $request->fecha_aplicacion,
            'fecha_proxima'     => $request->fecha_proxima,
            'dosis_aplicada'    => $request->dosis_aplicada ?? 0,
            'lote'              => $request->lote,
            'via_aplicacion'    => $request->via_aplicacion ?? 'oral',
            'costo'             => $request->costo ?? 0,
            'observaciones'     => $request->observaciones,
            'estado'            => 'aplicada',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Desparasitación registrada exitosamente',
            'aplicacion' => $aplicacion
        ]);
    }

    /**
     * Mostrar detalle
     */
       public function detalle($id)
    {
        $aplicacion = DesparasitacionAplicacion::with(['mascota', 'mascota.cliente', 'usuario', 'desparasitante'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'aplicacion' => [
                'id' => $aplicacion->id,
                'mascota' => $aplicacion->mascota->nombre ?? 'N/A',
                'mascota_especie' => $aplicacion->mascota->especie ?? 'N/A',
                'mascota_raza' => $aplicacion->mascota->raza ?? 'N/A',
                'cliente' => $aplicacion->mascota->cliente->nombreCompleto() ?? 'N/A',
                'veterinario' => $aplicacion->usuario->name ?? 'N/A',
                'producto' => $aplicacion->desparasitante->nombre ?? 'N/A',
                'tipo' => $aplicacion->desparasitante->tipo ?? 'N/A',
                
                // Para el modal VER (formato d/m/Y)
                'fecha_aplicacion' => $aplicacion->fecha_aplicacion ? \Carbon\Carbon::parse($aplicacion->fecha_aplicacion)->format('d/m/Y') : '—',
                'fecha_proxima' => $aplicacion->fecha_proxima ? \Carbon\Carbon::parse($aplicacion->fecha_proxima)->format('d/m/Y') : '—',
                
                // 🔥 PARA EL MODAL EDITAR (formato Y-m-d)
                'fecha_aplicacion_raw' => $aplicacion->fecha_aplicacion ? \Carbon\Carbon::parse($aplicacion->fecha_aplicacion)->format('Y-m-d') : '',
                'fecha_proxima_raw' => $aplicacion->fecha_proxima ? \Carbon\Carbon::parse($aplicacion->fecha_proxima)->format('Y-m-d') : '',
                
                'lote' => $aplicacion->lote,
                'dosis' => $aplicacion->dosis_aplicada,
                'costo' => $aplicacion->costo,
                'estado' => $aplicacion->estado,
                'observaciones' => $aplicacion->observaciones,
                'mascota_id' => $aplicacion->mascota_id,
                'veterinario_id' => $aplicacion->usuario_id,
                'desparasitante_id' => $aplicacion->desparasitante_id,
            ]
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $aplicacion = DesparasitacionAplicacion::findOrFail($id);
        
        $mascotas = Mascota::whereHas('cliente', function($q) {
            $q->where('tenant_id', auth()->user()->tenant_id ?? null);
        })->with('cliente')->get();

        $veterinarios = User::whereHas('roles', function($q) {
            $q->where('name', 'veterinarian');
        })->get();

        if ($veterinarios->isEmpty()) {
            $veterinarios = User::where('rol', 'veterinarian')
                ->orWhere('rol', 'Veterinario')
                ->get();
        }

        return view('modales.DesparasitacionAplicacion-form', compact('aplicacion', 'mascotas', 'veterinarios'));
    }

    /**
     * Actualizar
     */
       public function update(Request $request, $id)
    {
        $aplicacion = DesparasitacionAplicacion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'mascota_id'        => 'required|exists:mascotas,id',
            'usuario_id'        => 'required|exists:veterinarios,id', // 🔥 Validar contra veterinarios
            'desparasitante_id' => 'required|exists:desparasitantes,id',
            'fecha_aplicacion'  => 'required|date',
            'fecha_proxima'     => 'nullable|date|after:fecha_aplicacion',
            'dosis_aplicada'    => 'nullable|numeric|min:0',
            'lote'              => 'nullable|string|max:50',
            'via_aplicacion'    => 'nullable|string|max:50',
            'costo'             => 'nullable|numeric|min:0',
            'estado'            => 'required|in:aplicada,programada,cancelada',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $aplicacion->update($request->only([
            'mascota_id', 'usuario_id', 'desparasitante_id', 'fecha_aplicacion', 
            'fecha_proxima', 'dosis_aplicada', 'lote', 'via_aplicacion', 'costo', 
            'estado', 'observaciones'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Desparasitación actualizada exitosamente'
        ]);
    }

    public function buscar(Request $request)
    {
        $query = trim($request->get('q', ''));
        $tenantId = auth()->user()->tenant_id ?? 1;

        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        // Hacemos un JOIN manual para asegurar que encontramos la mascota
        $mascotas = \Illuminate\Support\Facades\DB::table('mascotas')
            ->leftJoin('clientes', 'clientes.id', '=', 'mascotas.cliente_id')
            ->where(function($q) use ($tenantId) {
                // Si la mascota tiene tenant, lo usamos. Si no, el del cliente.
                $q->where('mascotas.tenant_id', $tenantId)
                  ->orWhere('clientes.tenant_id', $tenantId);
            })
            ->where(function($q) use ($query) {
                // 🔥 Búsqueda por Mascota O Cliente
                $q->where('mascotas.nombre', 'LIKE', "%{$query}%")
                  ->orWhere('mascotas.numero_chip', 'LIKE', "%{$query}%")
                  ->orWhere('clientes.nombres', 'LIKE', "%{$query}%")
                  ->orWhere('clientes.apellidos', 'LIKE', "%{$query}%");
            })
            ->select('mascotas.id', 'mascotas.nombre as mascota_nombre', 'clientes.nombres as cliente_nombres', 'clientes.apellidos as cliente_apellidos')
            ->limit(15)
            ->get();

        return response()->json([
            'results' => $mascotas->map(function($m) {
                $cliente = trim(($m->cliente_nombres ?? '') . ' ' . ($m->cliente_apellidos ?? ''));
                if (empty($cliente)) $cliente = 'Sin dueño';

                return [
                    'id' => $m->id,
                    'text' => "{$cliente} - {$m->mascota_nombre}" 
                ];
            })
        ]);
    }

    /**
     * Eliminar
     */
    public function destroy($id)
    {
        $aplicacion = DesparasitacionAplicacion::findOrFail($id);
        $aplicacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Desparasitación eliminada exitosamente'
        ]);
    }

    /**
     * Obtener por mascota
     */
    public function getByMascota($mascotaId)
    {
        $aplicaciones = DesparasitacionAplicacion::where('mascota_id', $mascotaId)
            ->orderBy('fecha_aplicacion', 'desc')
            ->get();

        return response()->json($aplicaciones);
    }
}