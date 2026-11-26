<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private function tenantId(): int
    {
        return (int) (auth()->user()->tenant_id ?? 1);
    }

    /**
     * Consulta base de ventas "cobradas" del tenant.
     * - Tolera que la fecha esté en `fecha` o en `created_at` (o en ambas).
     * - Tolera estados distintos de 'pagada' (excluye solo los no cobrados).
     */
    private function ventasCobradas()
    {
        $tabla = (new Venta)->getTable();
        $q = DB::table($tabla);

        if (Schema::hasColumn($tabla, 'tenant_id')) {
            $q->where('tenant_id', $this->tenantId());
        }
        if (Schema::hasColumn($tabla, 'estado')) {
            $q->whereRaw("LOWER(COALESCE(estado,'')) NOT IN ('anulada','anulado','cancelada','cancelado','pendiente')");
        }
        return $q;
    }

    /** Expresión SQL con la fecha real de la venta. */
    private function exprFecha(): string
    {
        $tabla = (new Venta)->getTable();
        $tieneFecha   = Schema::hasColumn($tabla, 'fecha');
        $tieneCreated = Schema::hasColumn($tabla, 'created_at');

        if ($tieneFecha && $tieneCreated) return 'COALESCE(fecha, created_at)';
        return $tieneFecha ? 'fecha' : 'created_at';
    }

    private function totalDelDia(string $dia): float
    {
        return (float) $this->ventasCobradas()
            ->whereRaw('DATE(' . $this->exprFecha() . ') = ?', [$dia])
            ->sum('total');
    }

    public function index()
    {
        $hoy  = now()->toDateString();
        $ayer = now()->subDay()->toDateString();

        $pacientesHoy  = Cita::delDia($hoy)->distinct('mascota_id')->count('mascota_id');
        $pacientesAyer = Cita::delDia($ayer)->distinct('mascota_id')->count('mascota_id');

        $citasHoy  = Cita::delDia($hoy)->count();
        $citasAyer = Cita::delDia($ayer)->count();

        $ventasHoy  = $this->totalDelDia($hoy);
        $ventasAyer = $this->totalDelDia($ayer);

        $inventarioBajo      = Producto::stockBajo()->where('activo', true)->count();
        $mascotasRegistradas = Mascota::count();

        $pacientesRecientes = Mascota::with('cliente')
            ->latest('created_at')
            ->take(5)
            ->get();

        $inventarioBajoLista = Producto::stockBajo()->where('activo', true)
            ->orderBy('stock')
            ->take(3)
            ->get();

        // Ventas de los últimos 5 meses (incluye el actual); los meses sin ventas salen en 0
        $inicio = now()->startOfMonth()->subMonths(4);
        $expr   = $this->exprFecha();

        $porMes = $this->ventasCobradas()
            ->whereRaw("$expr >= ?", [$inicio->toDateString()])
            ->selectRaw("DATE_FORMAT($expr, '%Y-%m') as mes, SUM(total) as total")
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $ventasMeses = collect();
        for ($i = 0; $i < 5; $i++) {
            $m = $inicio->copy()->addMonths($i);
            $ventasMeses->push([
                'mes'   => $m->format('Y-m'),
                'label' => ucfirst($m->locale('es')->translatedFormat('M')),
                'total' => (float) ($porMes[$m->format('Y-m')] ?? 0),
            ]);
        }

        // Variación real contra ayer (null si ayer fue 0 y no se puede calcular)
        $delta = function ($hoy, $ayer) {
            if ($ayer == 0) return $hoy > 0 ? 100 : null;
            return round((($hoy - $ayer) / $ayer) * 100);
        };
        $deltaPacientes = $delta($pacientesHoy, $pacientesAyer);
        $deltaCitas     = $delta($citasHoy, $citasAyer);
        $deltaVentas    = $delta($ventasHoy, $ventasAyer);

        return view('inicio', compact(
            'pacientesHoy', 'pacientesAyer',
            'citasHoy', 'citasAyer',
            'ventasHoy', 'ventasAyer',
            'inventarioBajo', 'mascotasRegistradas',
            'pacientesRecientes', 'inventarioBajoLista',
            'ventasMeses',
            'deltaPacientes', 'deltaCitas', 'deltaVentas'
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