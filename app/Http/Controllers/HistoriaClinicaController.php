<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Examen;
use App\Models\HistoriaClinica;
use App\Models\Mascota;
use App\Models\VacunaAplicacion;
use App\Models\Desparasitante;
use App\Models\DesparasitacionAplicacion;
use App\Models\Vacuna;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $request  = request();
        $tenantId = auth()->user()->tenant_id ?? null;

        $mascota = Mascota::with('cliente')
            ->whereHas('cliente', fn ($q) => $q->where('tenant_id', $tenantId))
            ->find($mascotaId);

        if (!$mascota) {
            return redirect()->route('mascotas')
                ->with('error', 'La mascota no fue encontrada.');
        }

        // Consultas
        $historias = $mascota->historiasClinicas()
            ->with(['veterinario', 'archivos', 'formulaMedica'])
            ->orderByDesc('fecha_consulta')
            ->get();

       $historiaActual = null;

        if ($request->boolean('nueva')) {
            // Formulario en blanco para registrar una consulta nueva
            $historiaActual = null;
        } elseif ($request->filled('consulta')) {
            $historiaActual = $historias->firstWhere('id', (int) $request->consulta);
        } else {
            $historiaActual = $historias->first();
        }

        $proximaCita = Cita::where('mascota_id', $mascota->id)
            ->whereDate('fecha', '>=', now())
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->with('veterinario')
            ->first();

        // Exámenes y laboratorio
        $examenes = Examen::where('mascota_id', $mascota->id)
            ->orderByDesc('fecha')
            ->get();

        // Vacunas y desparasitaciones (aplicaciones + catálogos de la clínica)
        $vacunaciones = VacunaAplicacion::with(['vacuna', 'usuario'])
            ->where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_aplicacion')
            ->get();

        $desparasitaciones = DesparasitacionAplicacion::with(['desparasitante', 'usuario'])
            ->where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_aplicacion')
            ->get();

        $catalogoVacunas = Vacuna::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $catalogoDesparasitantes = Desparasitante::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        // Recetas
        $recetas = Receta::with(['medicamentos', 'veterinario'])
            ->where('mascota_id', $mascota->id)
            ->orderByDesc('fecha_emision')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $veterinarios = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $medicamentosCatalogo = Producto::where('activo', true)
            ->orderBy('nombre')
            ->limit(500)
            ->pluck('nombre');

        return view('historias', [
            'mascota'        => $mascota,
            'historias'      => $historias,
            'historiaActual' => $historiaActual,
            'proximaCita'    => $proximaCita,

            // nombres que lee la vista historias.blade.php
            'historia'       => $historiaActual,
            'historial'      => $historias,

            'examenes'                => $examenes,
            'vacunaciones'            => $vacunaciones,
            'desparasitaciones'       => $desparasitaciones,
            'catalogoVacunas'         => $catalogoVacunas,
            'catalogoDesparasitantes' => $catalogoDesparasitantes,

            'recetas'              => $recetas,
            'veterinarios'         => $veterinarios,
            'medicamentosCatalogo' => $medicamentosCatalogo,
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

    // ======================================================================
    //  CREAR / ACTUALIZAR CONSULTA DESDE LA VISTA "HISTORIAS"
    // ======================================================================

    public function store(Request $request, $mascota = null)
    {
        $id = $mascota
            ?? $request->input('mascota_id')
            ?? $request->query('mascota');

        $mascota = $this->mascotaDeLaClinica($id);

        $data = $request->validate($this->reglasConsulta());

        $historia = DB::transaction(function () use ($data, $mascota, $request) {
            $historia = $mascota->historiasClinicas()->create(
                $this->camposConsulta($data) + [
                    'tenant_id'      => auth()->user()->tenant_id ?? null,
                    'veterinario_id' => auth()->id(),
                    'fecha_consulta' => now(),
                ]
            );

            $this->vincularRecetas($historia, $data);
            $this->guardarArchivos($historia, $request);

            return $historia;
        });

        return $this->respuestaConsulta($request, $historia, 'Consulta registrada correctamente.');
    }

    public function update(Request $request, HistoriaClinica $historia)
    {
        $data = $request->validate($this->reglasConsulta());

        DB::transaction(function () use ($historia, $data, $request) {
            // No se cambian la fecha ni el veterinario que atendió
            $historia->update($this->camposConsulta($data));

            $this->vincularRecetas($historia, $data);
            $this->guardarArchivos($historia, $request);
        });

        return $this->respuestaConsulta($request, $historia, 'Consulta actualizada correctamente.');
    }

    // ======================================================================
    //  HELPERS
    // ======================================================================

    private function reglasConsulta(): array
    {
        return [
            'motivo'                  => 'nullable|string|max:500',
            'anamnesis'               => 'nullable|string',
            'diagnostico'             => 'nullable|string',
            'tratamiento'             => 'nullable|string',
            'temperatura'             => 'nullable|numeric|min:0',
            'frecuencia_cardiaca'     => 'nullable|integer|min:0',
            'frecuencia_respiratoria' => 'nullable|integer|min:0',
            'peso'                    => 'nullable|numeric|min:0',
            'condicion_corporal'      => 'nullable|string|max:20',
            'hidratacion'             => 'nullable|string|max:20',
            'examen_fisico'           => 'nullable|string',
            'observaciones'           => 'nullable|string',
            'proxima_cita'            => 'nullable|date',
            'recetas_ids'             => 'nullable|array',
            'recetas_ids.*'           => 'integer',
            'archivos'                => 'nullable|array',
            'archivos.*'              => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ];
    }

    /** Pasa los nombres del formulario a las columnas reales de historias_clinicas. */
    private function camposConsulta(array $d): array
    {
        return [
            'motivo_consulta'         => $d['motivo'] ?? null,
            'anamnesis'               => $d['anamnesis'] ?? null,
            'diagnostico'             => $d['diagnostico'] ?? null,
            'tratamiento'             => $d['tratamiento'] ?? null,
            'temperatura'             => $d['temperatura'] ?? null,
            'frecuencia_cardiaca'     => $d['frecuencia_cardiaca'] ?? null,
            'frecuencia_respiratoria' => $d['frecuencia_respiratoria'] ?? null,
            'peso'                    => $d['peso'] ?? null,
            'estado_corporal'         => $d['condicion_corporal'] ?? null,
            'hidratacion'             => $d['hidratacion'] ?? null,
            'examen_fisico'           => $d['examen_fisico'] ?? null,
            'observaciones'           => $d['observaciones'] ?? null,
            'proxima_cita_sugerida'   => !empty($d['proxima_cita'])
                                            ? Carbon::parse($d['proxima_cita'])->toDateString()
                                            : null,
        ];
    }

    /** Recetas creadas antes de guardar la consulta. */
    private function vincularRecetas(HistoriaClinica $historia, array $data): void
    {
        if (empty($data['recetas_ids'])) {
            return;
        }

        Receta::whereIn('id', $data['recetas_ids'])
            ->where('mascota_id', $historia->mascota_id)
            ->whereNull('historia_clinica_id')
            ->update(['historia_clinica_id' => $historia->id]);
    }

    /** Archivos adjuntos (radiografías, informes, etc.). */
    private function guardarArchivos(HistoriaClinica $historia, Request $request): void
    {
        foreach (array_filter((array) $request->file('archivos', [])) as $archivo) {
            $ruta = $archivo->store("historias/{$historia->id}", 'public');

            $historia->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta'            => $ruta,
                'tipo'            => strtolower($archivo->getClientOriginalExtension()) === 'pdf' ? 'pdf' : 'imagen',
                'peso_kb'         => round($archivo->getSize() / 1024),
            ]);
        }
    }

    private function respuestaConsulta(Request $request, HistoriaClinica $historia, string $mensaje)
    {
        $url = route('historias.show', [$historia->mascota_id, 'consulta' => $historia->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => $mensaje,
                'id'         => $historia->id,
                'redirect'   => $url,
                'url_update' => route('historias.update', $historia->id),
            ]);
        }

        return redirect($url)->with('success', $mensaje);
    }

    /** La mascota debe ser de un cliente de la clínica del usuario. */
    private function mascotaDeLaClinica($id): Mascota
    {
        abort_if(blank($id) || !is_numeric($id), 422, 'No se recibió la mascota (mascota_id inválido).');

        $mascota = Mascota::with('cliente')->find($id);
        abort_unless($mascota, 404, "La mascota {$id} no existe o no es visible para esta clínica.");

        $tenantId = auth()->user()->tenant_id ?? null;
        abort_unless(
            $mascota->cliente && $mascota->cliente->tenant_id == $tenantId,
            404,
            'La mascota pertenece a otra clínica.'
        );

        return $mascota;
    }

 
    public function pdf(HistoriaClinica $historia)
    {
        $historia->load(['mascota.cliente', 'veterinario', 'formulaMedica']);

        $pdf = Pdf::loadView('historia-clinica-pdf', ['historia' => $historia])
            ->setPaper('letter');

        return $pdf->download('historia_clinica_' . Str::slug($historia->mascota->nombre, '_') . '.pdf');
    }
}