<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
        $tenantId = auth()->user()->tenant_id ?? 1; // 🔥 Fallback a 1 por si es null

        // 1. Mascotas para el select
        $mascotas = Mascota::whereHas('cliente', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->with('cliente')->get();

        // 2. Vacunas para el select
        $vacunas = Vacuna::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->get();

        // 🔥 3. VETERINARIOS DESDE LA NUEVA TABLA
        $veterinarios = \App\Models\Veterinario::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('vacunacion', compact('mascotas', 'veterinarios', 'vacunas'));
    }

       // 🔥 1. Agregar botones en el datatable
    public function datatable(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        $consulta = DB::table('vacuna_aplicaciones as a')
            ->join('mascotas as m', 'm.id', '=', 'a.mascota_id')
            ->join('clientes as c', 'c.id', '=', 'm.cliente_id')
            ->join('vacunas as v', 'v.id', '=', 'a.vacuna_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.usuario_id')
            ->where('c.tenant_id', $tenantId)
            ->select([
                'a.id', 'a.fecha_aplicacion', 'a.fecha_proxima', 'a.lote', 'a.estado',
                'm.nombre as mascota_nombre',
                DB::raw("TRIM(CONCAT(c.nombres, ' ', COALESCE(c.apellidos, ''))) as cliente_nombre"),
                'v.nombre as producto',
                'u.name as veterinario_nombre',
            ]);

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
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="verVacuna(' . $r->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editarVacuna(' . $r->id . ')" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="eliminarVacuna(' . $r->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

   
    // 🔥 3. Método para actualizar la vacuna
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fecha_aplicacion' => 'required|date',
            'fecha_proxima' => 'nullable|date|after:fecha_aplicacion',
            'estado' => 'required|in:aplicada,programada,cancelada',
            'costo' => 'nullable|numeric|min:0',
            'via_aplicacion' => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::table('vacuna_aplicaciones')->where('id', $id)->update([
            'fecha_aplicacion' => $request->fecha_aplicacion,
            'fecha_proxima' => $request->fecha_proxima,
            'lote' => $request->lote,
            'via_aplicacion' => $request->via_aplicacion,
            'dosis_aplicada' => $request->dosis_aplicada ?? 1,
            'costo' => $request->costo ?? 0,
            'estado' => $request->estado,
            'observaciones' => $request->observaciones,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacuna actualizada exitosamente'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascota_id' => 'required|exists:mascotas,id',
            'usuario_id' => 'required|exists:users,id', // El select se llama veterinario_id en la vista, lo mapeamos aquí
            'vacuna_id' => 'required|exists:vacunas,id',
            'fecha_aplicacion' => 'required|date',
            'fecha_proxima' => 'nullable|date|after:fecha_aplicacion',
            'dosis_aplicada' => 'nullable|numeric|min:0',
            'lote' => 'nullable|string|max:50',
            'via_aplicacion' => 'nullable|string|max:50',
            'costo' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 🔥 GUARDAR EN LA TABLA DE APLICACIONES (Usando DB para evitar problemas del modelo)
        $id = DB::table('vacuna_aplicaciones')->insertGetId([
            'mascota_id' => $request->mascota_id,
            'usuario_id' => $request->usuario_id, 
            'vacuna_id' => $request->vacuna_id,
            'fecha_aplicacion' => $request->fecha_aplicacion,
            'fecha_proxima' => $request->fecha_proxima,
            'dosis_aplicada' => $request->dosis_aplicada ?? 1,
            'lote' => $request->lote,
            'via_aplicacion' => $request->via_aplicacion ?? 'subcutanea',
            'costo' => $request->costo ?? 0,
            'observaciones' => $request->observaciones,
            'estado' => 'aplicada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacuna registrada exitosamente'
        ]);
    }

    public function destroy($id)
    {
        DB::table('vacuna_aplicaciones')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de vacuna eliminado exitosamente'
        ]);
    }

        public function show($id) 
    {
        try {
            $vacuna = DB::table('vacuna_aplicaciones as a')
                ->join('mascotas as m', 'm.id', '=', 'a.mascota_id')
                ->join('clientes as c', 'c.id', '=', 'm.cliente_id')
                ->join('vacunas as v', 'v.id', '=', 'a.vacuna_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.usuario_id') 
                ->where('a.id', $id)
                ->select([
                    'a.id', 
                    'a.fecha_aplicacion', 
                    'a.fecha_proxima', 
                    'a.lote', 
                    'a.estado', 
                    'a.costo', 
                    'a.dosis_aplicada',
                    'a.observaciones',
                    'm.nombre as mascota_nombre',
                    'm.especie as mascota_especie',
                    'm.raza as mascota_raza',
                    DB::raw("TRIM(CONCAT(c.nombres, ' ', COALESCE(c.apellidos, ''))) as cliente_nombre"),
                    'v.nombre as producto',
                    'u.name as veterinario_nombre'
                ])->first();

            if (!$vacuna) {
                return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
            }

            // 🔥 NO FORMATEAR LAS FECHAS AQUÍ
            // Dejamos que se envíen como vienen de la BD (YYYY-MM-DD)

            return response()->json([
                'success' => true,
                'vacuna' => $vacuna
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error SQL: ' . $e->getMessage()
            ], 500);
        }
    }
   

}