<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();
        $ayer = now()->subDay()->toDateString();

        $pacientesHoy = Cita::delDia($hoy)->distinct('mascota_id')->count('mascota_id');
        $pacientesAyer = Cita::delDia($ayer)->distinct('mascota_id')->count('mascota_id');

        $citasHoy = Cita::delDia($hoy)->count();
        $citasAyer = Cita::delDia($ayer)->count();

        $ventasHoy = Venta::whereDate('created_at', $hoy)->where('estado', 'pagada')->sum('total');
        $ventasAyer = Venta::whereDate('created_at', $ayer)->where('estado', 'pagada')->sum('total');

        $inventarioBajo = Producto::stockBajo()->where('activo', true)->count();
        $mascotasRegistradas = Mascota::count();

        $pacientesRecientes = Mascota::with('cliente')
            ->latest('created_at')
            ->take(5)
            ->get();

        $inventarioBajoLista = Producto::stockBajo()->where('activo', true)
            ->orderBy('stock')
            ->take(3)
            ->get();

        $ventasUltimos5Meses = Venta::where('estado', 'pagada')
            ->where('created_at', '>=', now()->subMonths(5))
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), DB::raw('SUM(total) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('inicio', compact(
            'pacientesHoy', 'pacientesAyer',
            'citasHoy', 'citasAyer',
            'ventasHoy', 'ventasAyer',
            'inventarioBajo', 'mascotasRegistradas',
            'pacientesRecientes', 'inventarioBajoLista',
            'ventasUltimos5Meses'
        ));
    }

    public function eventos(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date',
        ]);

        $desde = Carbon::parse($request->start)->toDateString();
        $hasta = Carbon::parse($request->end)->toDateString();

        $colores = [
            'confirmada'  => '#22C55E',
            'pendiente'   => '#F59E0B',
            'en_atencion' => '#2F6FED',
            'cancelada'   => '#EF4444',
        ];

        $eventos = Cita::with('mascota.cliente')
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('hora_inicio')
            ->get()
            ->map(function ($cita) use ($colores) {
                $fecha  = Carbon::parse($cita->fecha)->toDateString();
                $inicio = Carbon::parse($cita->hora_inicio)->format('H:i:s');
                $fin    = $cita->hora_fin ? Carbon::parse($cita->hora_fin)->format('H:i:s') : null;

                return [
                    'id'    => $cita->id,
                    'title' => $cita->mascota->nombre ?? 'Cita',
                    'start' => "$fecha $inicio",
                    'end'   => $fin ? "$fecha $fin" : null,
                    'color' => $colores[$cita->estado] ?? '#64748B',
                    'extendedProps' => [
                        'fecha'   => $fecha,
                        'hora'    => Carbon::parse($cita->hora_inicio)->format('h:i A'),
                        'estado'  => $cita->estado,
                        'mascota' => $cita->mascota->nombre ?? '',
                        'raza'    => $cita->mascota->raza ?? '',
                        'cliente' => $cita->mascota->cliente->nombres ?? '',
                    ],
                ];
            });

        return response()->json($eventos);
    }
}