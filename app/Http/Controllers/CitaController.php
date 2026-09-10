<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Mascota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitaController extends Controller
{
    public function index()
    {
        return view('citas.index');
    }

    public function getEvents(Request $request)
    {
        $citas = Cita::with(['cliente', 'mascota', 'veterinario'])
            ->where('tenant_id', $request->tenant->id)
            ->whereBetween('fecha', [$request->start, $request->end])
            ->get();

        return response()->json($citas->map(function($cita) {
            return [
                'id' => $cita->id,
                'title' => $cita->mascota->nombre . ' - ' . $cita->cliente->nombres,
                'start' => $cita->fecha->format('Y-m-d') . 'T' . $cita->hora_inicio,
                'end' => $cita->fecha->format('Y-m-d') . 'T' . $cita->hora_fin,
                'backgroundColor' => $this->getColorByEstado($cita->estado),
                'borderColor' => $this->getColorByEstado($cita->estado),
                'extendedProps' => [
                    'cliente' => $cita->cliente->nombres . ' ' . $cita->cliente->apellidos,
                    'mascota' => $cita->mascota->nombre,
                    'veterinario' => $cita->veterinario->name,
                    'tipo' => Cita::TIPOS[$cita->tipo],
                    'estado' => Cita::ESTADOS[$cita->estado],
                    'motivo' => $cita->motivo,
                    'costo' => $cita->costo,
                ]
            ];
        }));
    }

    private function getColorByEstado($estado)
    {
        $colors = [
            'agendada' => '#3498db',
            'confirmada' => '#2ecc71',
            'en_curso' => '#f39c12',
            'completada' => '#27ae60',
            'cancelada' => '#e74c3c',
            'no_asistio' => '#95a5a6',
        ];
        return $colors[$estado] ?? '#3498db';
    }

    public function create()
    {
        $clientes = Cliente::where('tenant_id', request()->tenant->id)->get();
        $veterinarios = User::where('tenant_id', request()->tenant->id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'veterinarian');
            })->get();
            
        return view('citas.create', compact('clientes', 'veterinarios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'mascota_id' => 'required|exists:mascotas,id',
            'usuario_id' => 'required|exists:users,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'tipo' => 'required',
            'motivo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar disponibilidad
        $disponible = $this->checkAvailability(
            $request->usuario_id,
            $request->fecha,
            $request->hora_inicio,
            $request->hora_fin
        );

        if (!$disponible) {
            return response()->json([
                'success' => false,
                'message' => 'El veterinario no está disponible en ese horario'
            ], 422);
        }

        $cita = Cita::create([
            'tenant_id' => $request->tenant->id,
            ...$request->all()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita agendada exitosamente',
            'cita' => $cita
        ]);
    }

    private function checkAvailability($veterinarioId, $fecha, $horaInicio, $horaFin)
    {
        return !Cita::where('usuario_id', $veterinarioId)
            ->where('fecha', $fecha)
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                    ->orWhere(function($q) use ($horaInicio, $horaFin) {
                        $q->where('hora_inicio', '<=', $horaInicio)
                          ->where('hora_fin', '>=', $horaFin);
                    });
            })
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->exists();
    }

    public function updateStatus(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Cita::ESTADOS))
        ]);

        $cita->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la cita actualizado'
        ]);
    }
}