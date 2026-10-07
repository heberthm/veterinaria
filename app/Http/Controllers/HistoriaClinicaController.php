<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Examen;
use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\VacunaAplicacion;
use App\Models\Desparasitacion;
use App\Models\Desparasitante;
use App\Models\DesparasitacionAplicacion;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class HistoriaClinicaController extends Controller
{
    /**
     * Redirige a la historia de la primera mascota de la clínica.
     * Si no hay mascotas, muestra la vista vacía.
     */
    public function index()
    {
        $primera = Mascota::whereHas('cliente', function ($q) {
            $q->where('tenant_id', auth()->user()->tenant_id ?? null);
        })->first();

        if ($primera) {
            return redirect()->route('historias.show', $primera->id);
        }

        return view('historias');
    }

    /**
     * Vista de historias clínicas de una mascota.
     * Admite ?consulta=ID para abrir una consulta específica.
     */
    public function show($mascotaId)
    {
        $request = request();

        $mascota = Mascota::with('cliente')->find($mascotaId);

        if (!$mascota) {
            return redirect()->route('mascotas')
                ->with('error', 'La mascota no fue encontrada.');
        }

        $historias = $mascota->historiasClinicas()
            ->with(['veterinario', 'archivos', 'formulaMedica'])
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

        $examenes = Examen::where('mascota_id', $mascota->id)
            ->orderByDesc('fecha')
            ->get();

        $vacunas = VacunaAplicacion::where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_aplicacion')   // usa tu columna de fecha
            ->get();

        $DesparasitacionAplicaciones = DesparasitacionAplicacion::where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_aplicacion')   // usa tu columna de fecha
            ->get();

            $tenantId = auth()->user()->tenant_id ?? null;

            $VacunaAplicaciones = VacunaAplicacion::with(['vacuna', 'usuario'])
                ->where('mascota_id', $mascota->id)
                ->orderByDesc('fecha_aplicacion')
                ->get();

            $DesparasitacionAplicaciones = DesparasitacionAplicacion::with(['desparasitante', 'usuario'])
                ->where('mascota_id', $mascota->id)
                ->orderByDesc('fecha_aplicacion')
                ->get();

            $catalogoVacunas = Vacuna::where('tenant_id', $tenantId)->where('activo', true)->orderBy('nombre')->get();
            $catalogoDesparasitantes = Desparasitante::where('tenant_id', $tenantId)->where('activo', true)->orderBy('nombre')->get();



        return view('historias', [
            'mascota'        => $mascota,
            'historias'      => $historias,
            'historiaActual' => $historiaActual,
            'proximaCita'    => $proximaCita,           
            'historia'       => $historiaActual,
            'historial'      => $historias,
            'examenes'       => $examenes,
            'vacunas'           => $vacunas,
            'DesparasitacionAplicaciones' => $DesparasitacionAplicaciones,
            'VacunaAplicaciones'            => $VacunaAplicaciones,
            'DesparasitacionAplicaciones'       => $DesparasitacionAplicaciones,
            'catalogoVacunas'         => $catalogoVacunas,
            'catalogoDesparasitantes' => $catalogoDesparasitantes,
        ]);
    }

    // ======================================================================
    //  MODAL "EDITAR HISTORIA CLÍNICA" EN EL CRUD DE MASCOTAS
    // ======================================================================

    /**
     * Lista de consultas de una mascota (selector del modal).
     */
    public function porMascota(Mascota $mascota)
    {
        $historias = HistoriaClinica::where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_consulta')
            ->get(['id', 'fecha_consulta', 'motivo_consulta']);

        if ($historias->isEmpty()) {
            return response()->json(['message' => 'Esta mascota aún no tiene historia clínica.'], 404);
        }

        return response()->json([
            'mascota'   => ['id' => $mascota->id, 'nombre' => $mascota->nombre],
            'historias' => $historias->map(fn ($h) => [
                'id'       => $h->id,
                'etiqueta' => optional($h->fecha_consulta)->format('d/m/Y h:i A')
                              . ' · ' . ($h->motivo_consulta ?: 'Sin motivo'),
            ]),
        ]);
    }

    /**
     * Detalle de una consulta (llena el modal).
     */
    public function detalle(HistoriaClinica $historia)
    {
        $historia->load(['archivos', 'veterinario']);

        $tratamiento = $historia->tratamiento;
        $tratamientoTxt = is_array($tratamiento)
            ? json_encode($tratamiento, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : (string) $tratamiento;

        return response()->json([
            'historia' => [
                'id'                      => $historia->id,
                'fecha_consulta'          => optional($historia->fecha_consulta)->format('Y-m-d\TH:i'),
                'motivo_consulta'         => $historia->motivo_consulta,
                'anamnesis'               => $historia->anamnesis,
                'diagnostico'             => $historia->diagnostico,
                'tratamiento'             => $tratamientoTxt,
                'temperatura'             => $historia->temperatura,
                'frecuencia_cardiaca'     => $historia->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $historia->frecuencia_respiratoria,
                'peso'                    => $historia->peso,
                'estado_corporal'         => $historia->estado_corporal,
                'hidratacion'             => $historia->hidratacion,
                'examen_fisico'           => $historia->examen_fisico,
                'observaciones'           => $historia->observaciones,
                'proxima_cita_sugerida'   => optional($historia->proxima_cita_sugerida)->format('Y-m-d'),
                'veterinario'             => $historia->veterinario->name ?? null,
            ],
            'archivos' => $historia->archivos->map(fn ($a) => [
                'id'      => $a->id,
                'nombre'  => $a->nombre_original,
                'tipo'    => $a->tipo,
                'peso_kb' => $a->peso_kb,
                'url'     => Storage::url($a->ruta),
            ]),
        ]);
    }

    /**
     * Guarda los cambios hechos desde el modal de mascotas.
     */
    public function actualizarDesdeMascota(Request $request, HistoriaClinica $historia)
    {
        $data = $request->validate([
            'fecha_consulta'          => 'nullable|date',
            'motivo_consulta'         => 'nullable|string|max:500',
            'anamnesis'               => 'nullable|string',
            'diagnostico'             => 'nullable|string',
            'tratamiento'             => 'nullable|string',
            'temperatura'             => 'nullable|numeric|min:0',
            'frecuencia_cardiaca'     => 'nullable|integer|min:0',
            'frecuencia_respiratoria' => 'nullable|integer|min:0',
            'peso'                    => 'nullable|numeric|min:0',
            'estado_corporal'         => 'nullable|string|max:100',
            'hidratacion'             => 'nullable|string|max:100',
            'examen_fisico'           => 'nullable|string',
            'observaciones'           => 'nullable|string',
            'proxima_cita_sugerida'   => 'nullable|date',
        ]);

        // Si el tratamiento es JSON válido (lista de ítems) se guarda como array;
        // si no, queda como texto libre.
        if (!empty($data['tratamiento'])) {
            $decoded = json_decode($data['tratamiento'], true);
            if (is_array($decoded)) {
                $data['tratamiento'] = $decoded;
            }
        }

        $historia->update($data);

        return response()->json(['message' => 'Historia clínica actualizada.']);
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