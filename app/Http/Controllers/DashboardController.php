<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\Venta;
use App\Traits\GeneraCalendarioMensual;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use GeneraCalendarioMensual;

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

        $proximasCitas = Cita::with(['mascota.cliente'])
            ->delDia($hoy)
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_atencion'])
            ->orderBy('hora_inicio')
            ->take(4)
            ->get();

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

        $calendario = $this->generarCalendario(now());

        return view('inicio', compact(
            'pacientesHoy', 'pacientesAyer',
            'citasHoy', 'citasAyer',
            'ventasHoy', 'ventasAyer',
            'inventarioBajo', 'mascotasRegistradas',
            'proximasCitas', 'pacientesRecientes', 'inventarioBajoLista',
            'ventasUltimos5Meses', 'calendario'
        ));
    }
}
