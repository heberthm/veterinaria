<?php

namespace App\Http\Controllers;

use App\Models\Desparasitacion;
use App\Models\Mascota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DesparasitacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de desparasitaciones
     */
    public function index()
    {
        return view('desparasitaciones');
    }

    /**
     * DataTable para desparasitaciones
     */
    public function datatable(Request $request)
    {
        $desparasitaciones = Desparasitacion::with(['mascota', 'mascota.cliente', 'veterinario'])
            ->whereHas('mascota.cliente', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            });

        return DataTables::of($desparasitaciones)
            ->addColumn('mascota_nombre', function($desparasitacion) {
                return $desparasitacion->mascota->nombre ?? 'N/A';
            })
            ->addColumn('cliente_nombre', function($desparasitacion) {
                return $desparasitacion->mascota->cliente->nombreCompleto() ?? 'N/A';
            })
            ->addColumn('veterinario_nombre', function($desparasitacion) {
                return 'Dr. ' . ($desparasitacion->veterinario->name ?? 'N/A');
            })
            ->addColumn('fecha_aplicacion_formato', function($desparasitacion) {
                return $desparasitacion->fecha_aplicacion->format('d/m/Y');
            })
            ->addColumn('tipo_label', function($desparasitacion) {
                return Desparasitacion::TIPOS[$desparasitacion->tipo] ?? $desparasitacion->tipo;
            })
            ->addColumn('estado_badge', function($desparasitacion) {
                $estados = [
                    'aplicada' => 'success',
                    'pendiente' => 'warning',
                    'vencida' => 'danger'
                ];
                return '<span class="badge badge-' . ($estados[$desparasitacion->estado] ?? 'secondary') . '">' 
                    . Desparasitacion::ESTADOS[$desparasitacion->estado] . '</span>';
            })
            ->addColumn('acciones', function($desparasitacion) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verDesparasitacion(' . $desparasitacion->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-accion" onclick="editarDesparasitacion(' . $desparasitacion->id . ')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-accion" onclick="eliminarDesparasitacion(' . $desparasitacion->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

    /**
     * 🔥 MOSTRAR FORMULARIO DE CREACIÓN (MODAL) - DEPURAR
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

            // Si no hay veterinarios con rol, buscar por campo rol
            if ($veterinarios->isEmpty()) {
                $veterinarios = User::where('rol', 'veterinarian')
                    ->orWhere('rol', 'Veterinario')
                    ->get();
            }

            return view('modales.desparasitacion-form', compact('mascotas', 'veterinarios', 'mascota'));

        } catch (\Exception $e) {
            // 🔥 DEVOLVER EL ERROR PARA DEPURAR
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
            'mascota_id' => 'required|exists:mascotas,id',
            'veterinario_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:100',
            'fecha_aplicacion' => 'required|date',
            'fecha_proxima' => 'nullable|date|after:fecha_aplicacion',
            'tipo' => 'required|in:interna,externa,mixta',
            'costo' => 'nullable|numeric|min:0',
            'lote' => 'nullable|string|max:50',
            'laboratorio' => 'nullable|string|max:100',
            'dosis' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $desparasitacion = Desparasitacion::create([
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'mascota_id' => $request->mascota_id,
            'veterinario_id' => $request->veterinario_id,
            'nombre' => $request->nombre,
            'lote' => $request->lote,
            'fecha_aplicacion' => $request->fecha_aplicacion,
            'fecha_proxima' => $request->fecha_proxima,
            'tipo' => $request->tipo,
            'costo' => $request->costo ?? 0,
            'observaciones' => $request->observaciones,
            'laboratorio' => $request->laboratorio,
            'dosis' => $request->dosis,
            'estado' => 'aplicada',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Desparasitación registrada exitosamente',
            'desparasitacion' => $desparasitacion
        ]);
    }

    /**
     * Mostrar detalle
     */
    public function detalle($id)
    {
        $desparasitacion = Desparasitacion::with(['mascota', 'mascota.cliente', 'veterinario'])
            ->findOrFail($id);

        return view('modales.desparasitacion-detalle', compact('desparasitacion'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $desparasitacion = Desparasitacion::findOrFail($id);
        
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

        return view('modales.desparasitacion-form', compact('desparasitacion', 'mascotas', 'veterinarios'));
    }

    /**
     * Actualizar
     */
    public function update(Request $request, $id)
    {
        $desparasitacion = Desparasitacion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'mascota_id' => 'required|exists:mascotas,id',
            'veterinario_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:100',
            'fecha_aplicacion' => 'required|date',
            'fecha_proxima' => 'nullable|date|after:fecha_aplicacion',
            'tipo' => 'required|in:interna,externa,mixta',
            'costo' => 'nullable|numeric|min:0',
            'estado' => 'required|in:aplicada,pendiente,vencida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $desparasitacion->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Desparasitación actualizada exitosamente'
        ]);
    }

    /**
     * Eliminar
     */
    public function destroy($id)
    {
        $desparasitacion = Desparasitacion::findOrFail($id);
        $desparasitacion->delete();

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
        $desparasitaciones = Desparasitacion::where('mascota_id', $mascotaId)
            ->orderBy('fecha_aplicacion', 'desc')
            ->get();

        return response()->json($desparasitaciones);
    }
}