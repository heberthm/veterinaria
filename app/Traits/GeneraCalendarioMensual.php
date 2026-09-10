<?php

namespace App\Traits;

use App\Models\Cita;
use Carbon\Carbon;

trait GeneraCalendarioMensual
{
    /**
     * Genera la grilla de semanas (Lun-Dom) del mes indicado, marcando
     * el día actual, el día seleccionado y anexando los estados de las
     * citas que existan cada día (para pintar los "dots" de color).
     */
    protected function generarCalendario(Carbon $mes, ?Carbon $diaSeleccionado = null): array
    {
        $inicioMes = $mes->copy()->startOfMonth();
        $finMes = $mes->copy()->endOfMonth();

        $inicioGrilla = $inicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $finGrilla = $finMes->copy()->endOfWeek(Carbon::SUNDAY);

        $citasPorDia = Cita::whereBetween('fecha', [$inicioGrilla->toDateString(), $finGrilla->toDateString()])
            ->select('fecha', 'estado')
            ->get()
            ->groupBy(fn ($cita) => $cita->fecha->toDateString());

        $dias = [];
        $cursor = $inicioGrilla->copy();
        while ($cursor->lte($finGrilla)) {
            $fechaStr = $cursor->toDateString();
            $estadosDelDia = $citasPorDia->get($fechaStr, collect())
                ->pluck('estado')->unique()->values();

            $dias[] = [
                'dia'          => $cursor->day,
                'fecha'        => $fechaStr,
                'esDelMes'     => $cursor->month === $inicioMes->month,
                'esHoy'        => $cursor->isToday(),
                'esSeleccionado' => $diaSeleccionado && $cursor->isSameDay($diaSeleccionado),
                'estados'      => $estadosDelDia,
            ];
            $cursor->addDay();
        }

        return [
            'nombreMes' => $mes->translatedFormat('F Y'),
            'semanas'   => array_chunk($dias, 7),
        ];
    }
}