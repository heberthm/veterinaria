<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Sede;
use App\Models\User;
use App\Traits\GeneraCalendarioMensual;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgendaController extends Controller
{
    use GeneraCalendarioMensual;

    public function index(Request $request)
    {
        $fechaSeleccionada = $request->filled('fecha')
            ? Carbon::parse($request->fecha)
            : now();

        $inicioSemana = $fechaSeleccionada->copy()->startOfWeek(Carbon::MONDAY);
        $finSemana = $fechaSeleccionada->copy()->endOfWeek(Carbon::SUNDAY);

        $query = Cita::with(['mascota.cliente', 'veterinario', 'sede'])
            ->whereBetween('fecha', [$inicioSemana->toDateString(), $finSemana->toDateString()]);

        if ($request->filled('veterinario_id')) {
            $query->where('veterinario_id', $request->veterinario_id);
        }
        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $citasSemana = $query->orderBy('hora_inicio')->get();

        // Agrupadas por fecha (Y-m-d) para pintar cada columna del día
        $citasPorDia = $citasSemana->groupBy(fn ($cita) => $cita->fecha->toDateString());

        $diasSemana = collect();
        $cursor = $inicioSemana->copy();
        while ($cursor->lte($finSemana)) {
            $diasSemana->push($cursor->copy());
            $cursor->addDay();
        }

        // Conteo de citas por estado (de la semana visible, respetando filtros)
        $estadosDisponibles = ['confirmada', 'pendiente', 'en_atencion', 'finalizada', 'cancelada'];
        $conteoEstados = collect($estadosDisponibles)->mapWithKeys(function ($estado) use ($citasSemana) {
            return [$estado => $citasSemana->where('estado', $estado)->count()];
        });

        // Resumen del día seleccionado
        $citasDelDia = $citasPorDia->get($fechaSeleccionada->toDateString(), collect());
        $resumenDia = [
            'total'       => $citasDelDia->count(),
            'en_atencion' => $citasDelDia->where('estado', 'en_atencion')->count(),
            'finalizadas' => $citasDelDia->where('estado', 'finalizada')->count(),
            'pendientes'  => $citasDelDia->where('estado', 'pendiente')->count(),
        ];

        $citaSeleccionada = $request->filled('cita')
            ? $citasSemana->firstWhere('id', (int) $request->cita)
            : $citasDelDia->first();

        $calendario = $this->generarCalendario($fechaSeleccionada->copy(), $fechaSeleccionada);

        $veterinarios = User::role('Veterinario')->orderBy('name')->get();
        $sedes = Sede::where('activa', true)->get();
        $mascotas = Mascota::with('cliente')->orderBy('nombre')->get();

        return view('agenda', compact(
            'fechaSeleccionada', 'inicioSemana', 'finSemana', 'diasSemana',
            'citasPorDia', 'conteoEstados', 'resumenDia', 'citaSeleccionada',
            'calendario', 'veterinarios', 'sedes', 'mascotas'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mascota_id'      => 'required|exists:mascotas,id',
            'veterinario_id'  => 'nullable|exists:users,id',
            'sede_id'         => 'nullable|exists:sedes,id',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required',
            'hora_fin'        => 'nullable',
            'tipo_consulta'   => 'nullable|string|max:100',
            'motivo'          => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cita = Cita::create(array_merge($validator->validated(), [
            'estado' => 'pendiente',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cita creada correctamente.',
            'cita'    => $cita->load(['mascota.cliente', 'veterinario']),
        ]);
    }

    public function update(Request $request, Cita $cita)
    {
        $validator = Validator::make($request->all(), [
            'veterinario_id' => 'nullable|exists:users,id',
            'sede_id'        => 'nullable|exists:sedes,id',
            'fecha'          => 'required|date',
            'hora_inicio'    => 'required',
            'hora_fin'       => 'nullable',
            'tipo_consulta'  => 'nullable|string|max:100',
            'motivo'         => 'nullable|string|max:255',
            'notas'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cita->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada correctamente.',
            'cita'    => $cita->fresh(['mascota.cliente', 'veterinario']),
        ]);
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmada,en_atencion,finalizada,cancelada',
        ]);

        $cita->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la cita actualizado.',
            'estado'  => $cita->estado,
        ]);
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita cancelada y eliminada de la agenda.',
        ]);
    }
}
