<?php

namespace App\Http\Controllers;

use App\Models\Vacuna;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class VacunacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 🔥 OBTENER MASCOTAS PARA EL SELECT
        $mascotas = Mascota::whereHas('cliente', function($q) {
            $q->where('tenant_id', auth()->user()->tenant_id ?? null);
        })->with('cliente')->get();

        // 🔥 OBTENER VETERINARIOS PARA EL SELECT - ¡AGREGAR ESTO!
        $veterinarios = User::whereHas('roles', function($q) {
            $q->where('name', 'veterinarian');
        })->get();

        // 🔥 SI NO HAY VETERINARIOS CON ROL, BUSCAR POR CAMPO 'rol'
        if ($veterinarios->isEmpty()) {
            $veterinarios = User::where('rol', 'veterinarian')
                ->orWhere('rol', 'Veterinario')
                ->get();
        }

        // 🔥 PASAR AMBAS VARIABLES A LA VISTA
        return view('vacunacion', compact('mascotas', 'veterinarios'));
    }

    public function datatable(Request $request)
    {
        $vacunas = Vacuna::with(['mascota', 'mascota.cliente', 'veterinario'])
            ->whereHas('mascota.cliente', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            });

        return DataTables::of($vacunas)
            ->addColumn('mascota_nombre', function($vacuna) {
                return $vacuna->mascota->nombre ?? 'N/A';
            })
            ->addColumn('cliente_nombre', function($vacuna) {
                return $vacuna->mascota->cliente->nombreCompleto() ?? 'N/A';
            })
            ->addColumn('veterinario_nombre', function($vacuna) {
                return 'Dr. ' . ($vacuna->veterinario->name ?? 'N/A');
            })
            ->addColumn('fecha_aplicacion_formato', function($vacuna) {
                return $vacuna->fecha_aplicacion->format('d/m/Y');
            })
            ->addColumn('estado_badge', function($vacuna) {
                $estados = [
                    'aplicada' => 'success',
                    'pendiente' => 'warning',
                    'vencida' => 'danger'
                ];
                return '<span class="badge badge-' . ($estados[$vacuna->estado] ?? 'secondary') . '">' 
                    . Vacuna::ESTADOS[$vacuna->estado] . '</span>';
            })
            ->addColumn('vigente', function($vacuna) {
                return $vacuna->estaVigente() 
                    ? '<span class="badge badge-success">Vigente</span>' 
                    : '<span class="badge badge-danger">No vigente</span>';
            })
            ->addColumn('acciones', function($vacuna) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verVacuna(' . $vacuna->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-accion" onclick="editarVacuna(' . $vacuna->id . ')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-accion" onclick="eliminarVacuna(' . $vacuna->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'vigente', 'acciones'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascota_id' => 'required|exists:mascotas,id',
            'veterinario_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:100',
            'fecha_aplicacion' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after:fecha_aplicacion',
            'fecha_proxima' => 'nullable|date|after:fecha_aplicacion',
            'costo' => 'nullable|numeric|min:0',
            'lote' => 'nullable|string|max:50',
            'laboratorio' => 'nullable|string|max:100',
            'via_aplicacion' => 'nullable|string|max:50',
            'dosis' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $vacuna = Vacuna::create([
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'mascota_id' => $request->mascota_id,
            'veterinario_id' => $request->veterinario_id,
            'nombre' => $request->nombre,
            'lote' => $request->lote,
            'fecha_aplicacion' => $request->fecha_aplicacion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'fecha_proxima' => $request->fecha_proxima,
            'costo' => $request->costo ?? 0,
            'observaciones' => $request->observaciones,
            'laboratorio' => $request->laboratorio,
            'via_aplicacion' => $request->via_aplicacion,
            'dosis' => $request->dosis,
            'estado' => 'aplicada',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacuna registrada exitosamente',
            'vacuna' => $vacuna
        ]);
    }

    public function detalle($id)
    {
        $vacuna = Vacuna::with(['mascota', 'mascota.cliente', 'veterinario'])
            ->findOrFail($id);

        return view('modales.vacuna-detalle', compact('vacuna'));
    }

    public function destroy($id)
    {
        $vacuna = Vacuna::findOrFail($id);
        $vacuna->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vacuna eliminada exitosamente'
        ]);
    }
}