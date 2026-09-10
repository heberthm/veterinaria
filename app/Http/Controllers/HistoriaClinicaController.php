<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\Mascota;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HistoriaClinicaController extends Controller
{
    /**
     * Lista de historias clínicas (sin parámetros)
     */
    public function index()
    {
        // 🔥 OBTENER TODAS LAS MASCOTAS
        $mascotas = Mascota::whereHas('cliente', function($q) {
            $q->where('tenant_id', auth()->user()->tenant_id ?? null);
        })->with('cliente')->get();

        // Si hay mascotas, redirigir a la primera
        if ($mascotas->isNotEmpty()) {
            return redirect()->route('historias.show', $mascotas->first()->id);
        }

        // Si no hay mascotas, pasar $mascotas a la vista
        return view('historias', compact('mascotas'));
    }

    /**
     * Mostrar historias clínicas de una mascota específica
     */
    public function show($mascotaId)
    {
        $request = request();

        // 🔥 BUSCAR LA MASCOTA Y PASARLA A LA VISTA
        $mascota = Mascota::with(['cliente', 'historiasClinicas.veterinario', 'historiasClinicas.archivos'])
            ->find($mascotaId);

        if (!$mascota) {
            return redirect()->route('mascotas')
                ->with('error', 'La mascota no fue encontrada.');
        }

        $historias = $mascota->historiasClinicas()
            ->with(['veterinario', 'archivos'])
            ->orderByDesc('fecha_consulta')
            ->get();

        $historiaActual = $request->filled('consulta')
            ? $historias->firstWhere('id', (int) $request->consulta)
            : $historias->first();

        $proximaCita = Cita::where('mascota_id', $mascota->id)
            ->whereDate('fecha', '>=', now())
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->with('veterinario')
            ->first();

        // 🔥 PASAR TODAS LAS VARIABLES A LA VISTA
        return view('historias', compact(
            'mascota',
            'historias',
            'historiaActual',
            'proximaCita'
        ));
    }



    public function store(Mascota $mascota, Request $request)
    {
        $validated = $request->validate([
            'motivo_consulta'          => 'nullable|string',
            'anamnesis'                => 'nullable|string',
            'diagnostico'               => 'nullable|string',
            'tratamiento_texto'         => 'nullable|string',
            'temperatura'               => 'nullable|numeric',
            'frecuencia_cardiaca'       => 'nullable|integer',
            'frecuencia_respiratoria'   => 'nullable|integer',
            'peso'                      => 'nullable|numeric',
            'estado_corporal'           => 'nullable|string|max:20',
            'hidratacion'               => 'nullable|string|max:20',
            'examen_fisico'             => 'nullable|string',
            'observaciones'             => 'nullable|string',
            'proxima_cita_sugerida'     => 'nullable|date',
            'producto.*'                => 'nullable|string',
            'presentacion.*'            => 'nullable|string',
            'dosis.*'                   => 'nullable|string',
            'cantidad.*'                => 'nullable|string',
            'archivos.*'                => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);

        $historia = $mascota->historiasClinicas()->create([
            'veterinario_id'           => Auth::id(),
            'fecha_consulta'           => now(),
            'motivo_consulta'          => $validated['motivo_consulta'] ?? null,
            'anamnesis'                => $validated['anamnesis'] ?? null,
            'diagnostico'               => $validated['diagnostico'] ?? null,
            'tratamiento'               => $validated['tratamiento_texto'] ?? null,
            'temperatura'               => $validated['temperatura'] ?? null,
            'frecuencia_cardiaca'       => $validated['frecuencia_cardiaca'] ?? null,
            'frecuencia_respiratoria'   => $validated['frecuencia_respiratoria'] ?? null,
            'peso'                      => $validated['peso'] ?? null,
            'estado_corporal'           => $validated['estado_corporal'] ?? null,
            'hidratacion'               => $validated['hidratacion'] ?? null,
            'examen_fisico'             => $validated['examen_fisico'] ?? null,
            'observaciones'             => $validated['observaciones'] ?? null,
            'proxima_cita_sugerida'     => $validated['proxima_cita_sugerida'] ?? null,
        ]);

        // Fórmula médica (filas dinámicas del formulario)
        foreach ($request->input('producto', []) as $i => $producto) {
            if (blank($producto)) continue;
            $historia->formulaMedica()->create([
                'producto'      => $producto,
                'presentacion'  => $request->input("presentacion.$i"),
                'dosis'         => $request->input("dosis.$i"),
                'cantidad'      => $request->input("cantidad.$i"),
                'frecuencia'    => $request->input("frecuencia.$i"),
                'duracion'      => $request->input("duracion.$i"),
            ]);
        }

        // Archivos adjuntos (radiografías, informes, etc.)
        foreach ($request->file('archivos', []) as $archivo) {
            $ruta = $archivo->store("historias-clinicas/{$mascota->id}", 'public');
            $historia->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta'            => $ruta,
                'tipo'            => $archivo->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'imagen',
                'peso_kb'         => round($archivo->getSize() / 1024),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Consulta registrada correctamente.']);
        }

        return redirect()
            ->route('historias.index', $mascota)
            ->with('success', 'Consulta registrada correctamente.');
    }

    public function pdf(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load(['mascota.cliente', 'veterinario', 'formulaMedica']);

        $pdf = Pdf::loadView('historia-clinica-pdf', ['historia' => $historiaClinica])
            ->setPaper('letter');

        $nombreArchivo = 'historia-clinica-' . $historiaClinica->mascota->nombre . '-' . $historiaClinica->fecha_consulta->format('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
