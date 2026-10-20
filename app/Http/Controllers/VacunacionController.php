<?php

namespace App\Http\Controllers;

use App\Models\Vacuna;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
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
        $tenantId = auth()->user()->tenant_id ?? null;

        // Se consulta con joins directos (no depende de los nombres de las relaciones del modelo)
        $consulta = DB::table('vacuna_aplicaciones as a')
            ->join('mascotas as m', 'm.id', '=', 'a.mascota_id')
            ->join('clientes as c', 'c.id', '=', 'm.cliente_id')
            ->join('vacunas as p', 'p.id', '=', 'a.vacuna_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.usuario_id')
            ->where('c.tenant_id', $tenantId)
            ->select([
                'a.id', 'a.fecha_aplicacion', 'a.fecha_proxima', 'a.lote', 'a.estado',
                'm.nombre as mascota_nombre',
                DB::raw("TRIM(CONCAT(c.nombres, ' ', COALESCE(c.apellidos, ''))) as cliente_nombre"),
                'p.nombre as producto',
                'u.name as veterinario_nombre',
            ]);

        // Si la tabla usa soft deletes, se ocultan los eliminados
        if (Schema::hasColumn('vacuna_aplicaciones', 'deleted_at')) {
            $consulta->whereNull('a.deleted_at');
        }

        $fecha = fn ($v) => $v ? Carbon::parse($v)->format('d/m/Y') : '—';

        return DataTables::of($consulta)
            ->editColumn('veterinario_nombre', fn ($r) => $r->veterinario_nombre ? 'Dr. ' . $r->veterinario_nombre : 'N/A')
            ->addColumn('fecha_aplicacion_formato', fn ($r) => $fecha($r->fecha_aplicacion))
            ->addColumn('fecha_proxima_formato', fn ($r) => $fecha($r->fecha_proxima))
            ->addColumn('estado_badge', function ($r) {
                $color = ['aplicada' => 'success', 'programada' => 'warning', 'cancelada' => 'secondary'][$r->estado] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . e(ucfirst((string) $r->estado)) . '</span>';
            })
            ->addColumn('acciones', function ($r) {
                return '<button class="btn btn-danger btn-sm btn-accion" onclick="eliminarVacuna(' . (int) $r->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>';
            })
            ->filterColumn('cliente_nombre', fn ($q, $k) => $q->whereRaw("CONCAT(c.nombres, ' ', COALESCE(c.apellidos, '')) like ?", ["%{$k}%"]))
            ->rawColumns(['estado_badge', 'acciones'])
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