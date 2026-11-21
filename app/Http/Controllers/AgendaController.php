<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Veterinario; 
use App\Models\Sede;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;



class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vista principal (solo carga el shell, sin datos del grid)
     */
 

public function index(Request $request)
{
    $tenantId = auth()->user()->tenant_id;

    // 🔥 CAMBIO AQUÍ: Buscar en la nueva tabla
    $veterinarios = Veterinario::where('tenant_id', $tenantId)
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();

    $sedes = Sede::where('tenant_id', $tenantId)->orderBy('nombre')->get();
    
    $mascotas = Mascota::whereHas('cliente', function ($q) use ($tenantId) {
        $q->where('tenant_id', $tenantId);
    })->with('cliente')->orderBy('nombre')->get();

    $fechaSeleccionada = $request->filled('fecha')
        ? Carbon::parse($request->fecha)
        : Carbon::today();

    return view('agenda', compact('veterinarios', 'sedes', 'mascotas', 'fechaSeleccionada'));
}

    public function eventos(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

        $start = $request->get('start');
        $end   = $request->get('end');

        $query = Cita::where('tenant_id', $tenantId);

        if ($start && $end) {
            $query->whereDate('fecha', '>=', Carbon::parse($start)->toDateString())
                  ->whereDate('fecha', '<=', Carbon::parse($end)->toDateString());
        }

        if ($request->filled('veterinario_id')) $query->where('veterinario_id', $request->veterinario_id);
        if ($request->filled('sede_id'))        $query->where('sede_id', $request->sede_id);
        if ($request->filled('estado'))         $query->where('estado', $request->estado);

        $citas = $query->with(['mascota.cliente', 'veterinario', 'sede'])->get();

        $colores = [
            'pendiente'   => ['bg' => '#F59E0B', 'border' => '#F59E0B'],
            'confirmada'  => ['bg' => '#16A34A', 'border' => '#16A34A'],
            'en_atencion' => ['bg' => '#2F6FED', 'border' => '#2F6FED'],
            'finalizada'  => ['bg' => '#8B5CF6', 'border' => '#8B5CF6'],
            'cancelada'   => ['bg' => '#EF4444', 'border' => '#EF4444'],
        ];

        $eventos = $citas->map(function ($cita) use ($colores) {
            // 🔥 1. Formatear correctamente la fecha y horas
            $fechaStr = $cita->fecha->format('Y-m-d');
            
            // Si la hora viene vacía, ponemos una por defecto
            $horaIni = $cita->hora_inicio ? substr($cita->hora_inicio, 0, 5) : '09:00';
            $horaFin = $cita->hora_fin ? substr($cita->hora_fin, 0, 5) : '09:30';

            // Construir fechas ISO 8601 que FullCalendar entiende perfectamente
            $inicio = "{$fechaStr}T{$horaIni}:00";
            $fin    = "{$fechaStr}T{$horaFin}:00";

            $color = $colores[$cita->estado] ?? ['bg' => '#6B7280', 'border' => '#6B7280'];

            return [
                'id'              => $cita->id,
                'title'           => $cita->mascota?->nombre ?? 'Cita',
                'start'           => $inicio,
                'end'             => $fin,
                'allDay'          => false, // 🔥 FORZAR FALSO PARA QUE SALGA EN LA SEMANA/DÍA
                'backgroundColor' => $color['bg'],
                'borderColor'     => $color['border'],
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'estado'          => $cita->estado,
                    'mascota_id'      => $cita->mascota_id,
                    'mascota'         => $cita->mascota?->nombre ?? '—',
                    'mascota_raza'    => $cita->mascota?->raza ?? '',
                    'mascota_especie' => $cita->mascota?->especie ?? '',
                    'cliente'         => $cita->mascota?->cliente?->nombreCompleto() ?? '—',
                    'cliente_tel'     => $cita->mascota?->cliente?->celular,
                    'veterinario_id'  => $cita->veterinario_id,
                    'veterinario'     => $cita->veterinario 
                                            ? 'Dr. ' . $cita->veterinario->nombre . ' ' . $cita->veterinario->apellidos 
                                            : 'Sin asignar',
                    'sede_id'         => $cita->sede_id,
                    'sede'            => $cita->sede->nombre ?? '—',
                    'tipo_consulta'   => $cita->tipo_consulta ?? 'Consulta general',
                    'motivo'          => $cita->motivo ?? 'Sin motivo registrado.',
                    'hora_inicio_fmt' => Carbon::parse($horaIni)->format('h:i A'),
                    'hora_fin_fmt'    => Carbon::parse($horaFin)->format('h:i A'),
                ],
            ];
        });

        return response()->json($eventos);
    }

/**
 * 🔥 AJAX: Datos para la vista de Agenda (mini calendario + resumen)
 */
public function datos(Request $request)
{
    $tenantId = auth()->user()->tenant_id;

    $fechaSeleccionada = $request->filled('fecha')
        ? Carbon::parse($request->fecha)
        : Carbon::today();

    // Rango de la semana
    $inicioSemana = $fechaSeleccionada->copy()->startOfWeek(Carbon::MONDAY);
    $finSemana    = $fechaSeleccionada->copy()->endOfWeek(Carbon::SUNDAY);

    // Query
    $queryCitas = Cita::where('tenant_id', $tenantId)
        ->whereDate('fecha', '>=', $inicioSemana->toDateString())
        ->whereDate('fecha', '<=', $finSemana->toDateString());

    if ($request->filled('veterinario_id')) $queryCitas->where('veterinario_id', $request->veterinario_id);
    if ($request->filled('sede_id'))        $queryCitas->where('sede_id', $request->sede_id);
    if ($request->filled('estado'))         $queryCitas->where('estado', $request->estado);

    $citas = $queryCitas->with(['mascota.cliente', 'veterinario', 'sede'])
        ->orderBy('fecha')
        ->orderBy('hora_inicio')
        ->get();

    $citasPorDia = $citas->groupBy(function ($cita) {
        return Carbon::parse($cita->fecha)->toDateString();
    });

    // Días de la semana
    $diasSemana = [];
    for ($i = 0; $i < 7; $i++) {
        $d = $inicioSemana->copy()->addDays($i);
        $diasSemana[] = [
            'iso'   => $d->toDateString(),
            'label' => $d->translatedFormat('D'),
            'dia'   => $d->day,
            'hoy'   => $d->isSameDay($fechaSeleccionada),
        ];
    }

    $conteoEstados = $citas->groupBy('estado')->map->count();

    $citasDelDia = $citasPorDia->get($fechaSeleccionada->toDateString(), collect());

    $resumenDia = [
        'total'       => $citasDelDia->count(),
        'en_atencion' => $citasDelDia->where('estado', 'en_atencion')->count(),
        'finalizadas' => $citasDelDia->where('estado', 'finalizada')->count(),
        'pendientes'  => $citasDelDia->where('estado', 'pendiente')->count(),
    ];

    $calendario = $this->construirMiniCalendario($fechaSeleccionada, $tenantId);

    $citaSeleccionada = null;
    if ($request->filled('cita_id')) {
        $citaSeleccionada = Cita::with(['mascota.cliente', 'veterinario', 'sede'])
            ->where('tenant_id', $tenantId)
            ->find($request->cita_id);
    } else {
        $citaSeleccionada = $citasDelDia->first();
    }

    return response()->json([
        'success'           => true,
        'fechaSeleccionada' => $fechaSeleccionada->toDateString(),
        'tituloFecha'       => ucfirst($fechaSeleccionada->translatedFormat('l, d \d\e F \d\e Y')),
        'diasSemana'        => $diasSemana,
        'citasPorDia'       => $citasPorDia->map(function ($items) {
            return $items->map(function ($c) {
                return [
                    'id'          => $c->id,
                    'mascota'     => $c->mascota->nombre ?? '—',
                    'raza'        => $c->mascota->raza ?? $c->mascota->especie ?? '',
                    'hora_inicio' => $c->hora_inicio,
                    'hora_fin'    => $c->hora_fin,
                    'estado'      => $c->estado,
                ];
            });
        }),
        'conteoEstados'     => [
            'confirmada'  => $conteoEstados['confirmada']  ?? 0,
            'pendiente'   => $conteoEstados['pendiente']   ?? 0,
            'en_atencion' => $conteoEstados['en_atencion'] ?? 0,
            'finalizada'  => $conteoEstados['finalizada']  ?? 0,
            'cancelada'   => $conteoEstados['cancelada']   ?? 0,
        ],
        'resumenDia'        => $resumenDia,
        'calendario'        => $calendario,
        'citaSeleccionada'  => $citaSeleccionada ? $this->formatearCita($citaSeleccionada) : null,
        'config'            => [
            'horaInicioGrid' => 8,
            'horaFinGrid'    => 18,
            'pxPorHora'      => 64,
        ],
    ]);
}

    /**
     * 🔥 AJAX: Guardar nueva cita
     */
        public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascota_id' => 'required|exists:mascotas,id',
            
            // 🔥 CAMBIO AQUÍ: Validar contra la tabla 'veterinarios'
            'veterinario_id' => 'nullable|exists:veterinarios,id', 
            
            'sede_id' => 'nullable|exists:sedes,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'tipo_consulta' => 'required|string',
            'estado' => 'required|string',
            'motivo' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cita = Cita::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'mascota_id' => $request->mascota_id,
            'veterinario_id' => $request->veterinario_id ?: null,
            'sede_id' => $request->sede_id ?: null,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'tipo_consulta' => $request->tipo_consulta,
            'estado' => $request->estado,
            'motivo' => $request->motivo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita guardada',
            'cita' => $cita
        ]);
    }

    /**
     * 🔥 AJAX: Actualizar cita
     */
        public function update(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id ?? 1;
        
        // 1. Buscar la cita
        $cita = Cita::where('tenant_id', $tenantId)->find($id);

        if (!$cita) {
            return response()->json([
                'success' => false, 
                'message' => 'Cita no encontrada'
            ], 404);
        }

        // 2. Validar los datos (quitando el veterinario_id duplicado)
        $validator = Validator::make($request->all(), [
            'mascota_id'     => 'required|exists:mascotas,id',
            'veterinario_id' => 'nullable|exists:veterinarios,id',
            'sede_id'        => 'nullable|exists:sedes,id',
            'fecha'          => 'required|date',
            'hora_inicio'    => 'required',
            'hora_fin'       => 'nullable',
            'tipo_consulta'  => 'nullable|string|max:100',
            'motivo'         => 'nullable|string',
            'estado'         => 'nullable|in:pendiente,confirmada,en_atencion,finalizada,cancelada',
        ]);

        // 3. Si la validación falla, devolver los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'errors'  => $validator->errors()
            ], 422);
        }

        // 4. Actualizar los datos en la base de datos
        try {
            $cita->update([
                'mascota_id'     => $request->mascota_id,
                'veterinario_id' => $request->veterinario_id ?: null, // Si viene vacío, guarda NULL
                'sede_id'        => $request->sede_id ?: null,
                'fecha'          => $request->fecha,
                'hora_inicio'    => $request->hora_inicio,
                'hora_fin'       => $request->hora_fin,
                'tipo_consulta'  => $request->tipo_consulta,
                'motivo'         => $request->motivo,
                'estado'         => $request->estado ?? $cita->estado,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada correctamente',
                'cita'    => $this->formatearCita($cita->fresh()),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * 🔥 AJAX: Cambiar estado
     */
    public function cambiarEstado(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;

        $cita = Cita::where('tenant_id', $tenantId)->findOrFail($id);

        $estado = $request->get('estado');
        if (!in_array($estado, ['pendiente', 'confirmada', 'en_atencion', 'finalizada', 'cancelada'])) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido',
            ], 422);
        }

        $cita->update(['estado' => $estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'estado'  => $estado,
        ]);
    }

    /**
     * 🔥 AJAX: Eliminar cita
     */
    public function destroy($id)
    {
        $tenantId = Auth::user()->tenant_id;

        $cita = Cita::where('tenant_id', $tenantId)->findOrFail($id);
        $cita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita eliminada',
        ]);
    }

   

    // ============================================================
    // HELPERS
    // ============================================================

    private function formatearCita(Cita $cita): array
        {
            return [
                'id'              => $cita->id,
                'estado'          => $cita->estado,
                'mascota_id'      => $cita->mascota_id,
                'mascota'         => $cita->mascota?->nombre ?? '—',
                'mascota_raza'    => $cita->mascota?->raza ?? '',
                'mascota_especie' => $cita->mascota?->especie ?? '',
                'cliente'         => $cita->mascota?->cliente?->nombreCompleto() ?? '—',
                'cliente_tel'     => $cita->mascota?->cliente?->celular,
                'veterinario_id'  => $cita->veterinario_id,
                'veterinario'     => $cita->veterinario?->name ?? 'Sin asignar',
                'sede_id'         => $cita->sede_id,
                'sede'            => $cita->sede?->nombre ?? '—',
                'tipo_consulta'   => $cita->tipo_consulta ?? 'Consulta general',
                'motivo'          => $cita->motivo ?? 'Sin motivo registrado.',
                'fecha'           => $cita->fecha?->format('Y-m-d'),
                'hora_inicio'     => $cita->hora_inicio,
                'hora_fin'        => $cita->hora_fin,
                'hora_inicio_fmt' => $cita->hora_inicio ? Carbon::parse($cita->hora_inicio)->format('h:i A') : '',
                'hora_fin_fmt'    => $cita->hora_fin ? Carbon::parse($cita->hora_fin)->format('h:i A') : '',
            ];
        }

    // ============================================================
    // MOVER CITA
    // ============================================================     

        public function mover(Request $request, $id)
        {
            $data = $request->validate([
                'fecha'       => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin'    => 'nullable|date_format:H:i',
            ]);

            $cita = Cita::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);

            try {
                $cita->update($data);
            } catch (\Illuminate\Database\QueryException $e) {
                // 1062 = ya existe otra cita de esa mascota en ese horario
                if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                    return response()->json([
                        'message' => 'Ya existe una cita para esta mascota en ese día y hora.',
                    ], 422);
                }
                throw $e;
            }

            return response()->json(['success' => true]);
        }

   /**
 * Construye el mini calendario del mes de la fecha seleccionada.
 */
    private function construirMiniCalendario(Carbon $fechaSeleccionada, $tenantId): array
    {
        $inicioMes = $fechaSeleccionada->copy()->startOfMonth();
        $finMes    = $fechaSeleccionada->copy()->endOfMonth();

        // Se muestran semanas completas (lunes a domingo)
        $inicioGrid = $inicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $finGrid    = $finMes->copy()->endOfWeek(Carbon::SUNDAY);

        // Estados de citas por día, en una sola consulta
        $citasPorFecha = Cita::where('tenant_id', $tenantId)
            ->whereDate('fecha', '>=', $inicioGrid->toDateString())
            ->whereDate('fecha', '<=', $finGrid->toDateString())
            ->get(['fecha', 'estado'])
            ->groupBy(fn ($c) => Carbon::parse($c->fecha)->toDateString());

        $semanas = [];
        $dia = $inicioGrid->copy();

        while ($dia->lte($finGrid)) {
            $semana = [];

            for ($i = 0; $i < 7; $i++) {
                $iso = $dia->toDateString();

                $semana[] = [
                    'fecha'          => $iso,
                    'dia'            => $dia->day,
                    'esDelMes'       => $dia->month === $fechaSeleccionada->month,
                    'esSeleccionado' => $dia->isSameDay($fechaSeleccionada),
                    'estados'        => $citasPorFecha->has($iso)
                        ? $citasPorFecha[$iso]->pluck('estado')->unique()->values()->all()
                        : [],
                ];

                $dia->addDay();
            }

            $semanas[] = $semana;
        }

        return [
            'nombreMes' => ucfirst($fechaSeleccionada->translatedFormat('F Y')),
            'semanas'   => $semanas,
        ];
    }

  
}