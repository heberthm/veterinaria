<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class RecetaController extends Controller
{
    public function store(Request $request, Mascota $mascota)
    {
        $this->autorizarMascota($mascota->id);
        $tenantId = auth()->user()->tenant_id ?? null;

        // Ignora las filas de medicamentos completamente vacías
        $request->merge([
            'medicamentos' => array_values(array_filter(
                (array) $request->input('medicamentos', []),
                fn ($f) => is_array($f) && (
                    filled($f['medicamento'] ?? null) || filled($f['dosis'] ?? null) ||
                    filled($f['frecuencia'] ?? null) || filled($f['duracion_dias'] ?? null) ||
                    filled($f['instrucciones'] ?? null)
                )
            )),
        ]);

        $data = $request->validate([
            'fecha_emision'          => 'required|date',
            'veterinario_id'         => ['required', Rule::exists('users', 'id')->where('tenant_id', $tenantId)],
            'historia_clinica_id'    => ['nullable', Rule::exists('historias_clinicas', 'id')->where('mascota_id', $mascota->id)],
            
            'indicaciones_generales' => 'nullable|string',
            'observaciones'          => 'nullable|string',
            'medicamentos'                   => 'required|array|min:1',
            'medicamentos.*.medicamento'     => 'required|string|max:150',
            'medicamentos.*.dosis'           => 'required|string|max:100',
            'medicamentos.*.frecuencia'      => 'nullable|string|max:50',
            'medicamentos.*.duracion_dias'   => 'nullable|integer|min:1|max:365',
            'medicamentos.*.instrucciones'   => 'nullable|string|max:255',
        ], [
            'medicamentos.required'                => 'Agrega al menos un medicamento.',
            'medicamentos.min'                     => 'Agrega al menos un medicamento.',
            'medicamentos.*.medicamento.required'  => 'Indica el nombre de cada medicamento.',
            'medicamentos.*.dosis.required'        => 'Indica la dosis de cada medicamento.',
        ]);

        $receta = DB::transaction(function () use ($data, $mascota, $tenantId) {
            $receta = Receta::create([
                'tenant_id'              => $tenantId,
                'mascota_id'             => $mascota->id,
                'historia_clinica_id'    => $data['historia_clinica_id'] ?? null,
                'veterinario_id'         => $data['veterinario_id'],
                'fecha_emision'          => $data['fecha_emision'],
                'indicaciones_generales' => $data['indicaciones_generales'] ?? null,
                'observaciones'          => $data['observaciones'] ?? null,
            ]);

            $receta->medicamentos()->createMany($data['medicamentos']);

            return $receta;
        });

        $receta->load(['medicamentos', 'veterinario']);

        return response()->json([
            'message' => 'Receta guardada.',
            'receta'  => [
                'id'           => $receta->id,
                'fecha'        => $receta->fecha_emision->format('d/m/Y'),
                'medico'       => $receta->veterinario->name ?? '—',
                'estado'       => $receta->estado,
                'indicaciones' => $receta->indicaciones_generales,
                'url_borrar'   => route('recetas.destroy', $receta->id),
                'medicamentos' => $receta->medicamentos->map(fn ($m) => [
                    'medicamento'   => $m->medicamento,
                    'dosis'         => $m->dosis,
                    'frecuencia'    => $m->frecuencia,
                    'duracion_dias' => $m->duracion_dias,
                    'instrucciones' => $m->instrucciones,
                ]),
            ],
        ]);
    }

   public function show(Request $request, Receta $receta)
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        // Carga la mascota SI existe; si no, no revienta
        $receta->load(['medicamentos', 'veterinario', 'mascota.cliente']);

        // Seguridad opcional: si hay mascota y cliente, valida tenant
        if ($receta->mascota && $receta->mascota->cliente) {
            abort_unless($receta->mascota->cliente->tenant_id == $tenantId, 404);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'receta' => [
                    'id'                  => $receta->id,
                    'fecha'               => optional($receta->fecha_emision)->format('d/m/Y'),
                    'fecha_iso'           => optional($receta->fecha_emision)->format('Y-m-d'),
                    'veterinario_id'      => $receta->veterinario_id,
                    'medico'              => $receta->veterinario->name ?? '—',
                    'estado'              => $receta->estado ?? 'activa',
                    'indicaciones'        => $receta->indicaciones_generales,
                    'observaciones'       => $receta->observaciones,
                    'historia_clinica_id' => $receta->historia_clinica_id,
                    'medicamentos'        => $receta->medicamentos->map(fn ($m) => [
                        'medicamento'   => $m->medicamento,
                        'dosis'         => $m->dosis,
                        'frecuencia'    => $m->frecuencia,
                        'duracion_dias' => $m->duracion_dias,
                        'instrucciones' => $m->instrucciones,
                    ])->values(),
                    'url'     => route('recetas.update', $receta->id),
                    'url_pdf' => route('recetas.pdf', $receta->id),                 
                    'url_pdf_preview' => route('recetas.pdf.preview', $receta->id),
                ],
            ]);
        }

        return redirect()->route('mascotas');
    }

    public function update(Request $request, Receta $receta)
    {
        // Opcional: valida tenant solo si hay mascota y cliente
        $tenantId = auth()->user()->tenant_id ?? null;
        if ($receta->mascota && $receta->mascota->cliente) {
            abort_unless($receta->mascota->cliente->tenant_id == $tenantId, 404);
        }

        $data = $request->validate([
            'fecha_emision'          => 'required|date',
            'veterinario_id'         => 'required|integer|exists:users,id',
            'indicaciones_generales' => 'nullable|string',
            'observaciones'          => 'nullable|string',
            'historia_clinica_id'    => 'nullable|integer|exists:historias_clinicas,id',  // sin where

            'medicamentos'                 => 'required|array|min:1',
            'medicamentos.*.medicamento'   => 'required|string|max:150',
            'medicamentos.*.dosis'         => 'required|string|max:100',
            'medicamentos.*.frecuencia'    => 'nullable|string|max:50',
            'medicamentos.*.duracion_dias' => 'nullable|integer|min:1|max:365',
            'medicamentos.*.instrucciones' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($receta, $data) {
            $receta->fill([
                'fecha_emision'          => $data['fecha_emision'],
                'veterinario_id'         => $data['veterinario_id'],
                'indicaciones_generales' => $data['indicaciones_generales'] ?? null,
                'observaciones'          => $data['observaciones'] ?? null,
            ]);

            if (!empty($data['historia_clinica_id'])) {
                $receta->historia_clinica_id = $data['historia_clinica_id'];
            }

            $receta->save();

            $receta->medicamentos()->delete();
            foreach ($data['medicamentos'] as $m) {
                $receta->medicamentos()->create([
                    'medicamento'   => $m['medicamento'],
                    'dosis'         => $m['dosis'],
                    'frecuencia'    => $m['frecuencia'] ?? null,
                    'duracion_dias' => $m['duracion_dias'] ?? null,
                    'instrucciones' => $m['instrucciones'] ?? null,
                ]);
            }
        });

        // Respuesta
        $receta->load(['medicamentos', 'veterinario']);

        return response()->json([
            'success' => true,
            'message' => 'Receta actualizada correctamente.',
            'receta'  => [
                'id'            => $receta->id,
                'fecha'         => optional($receta->fecha_emision)->format('d/m/Y'),
                'fecha_iso'     => optional($receta->fecha_emision)->format('Y-m-d'),
                'medico'        => $receta->veterinario->name ?? '—',
                'estado'        => $receta->estado ?? 'activa',
                'indicaciones'  => $receta->indicaciones_generales,
                'observaciones' => $receta->observaciones,
                'medicamentos'  => $receta->medicamentos->map(fn ($m) => [
                    'medicamento'   => $m->medicamento,
                    'dosis'         => $m->dosis,
                    'frecuencia'    => $m->frecuencia,
                    'duracion_dias' => $m->duracion_dias,
                    'instrucciones' => $m->instrucciones,
                ])->values(),
                'url'     => route('recetas.update', $receta->id),
                'url_pdf' => route('recetas.pdf', $receta->id),
            ],
        ]);
    }
    

    public function destroy(Receta $receta)
    {
        $this->autorizarMascota($receta->mascota_id);
        $receta->delete();   // los medicamentos se borran en cascada

        return response()->json(['message' => 'Receta eliminada.']);
    }

    /** La mascota debe ser de un cliente de la clínica del usuario. */
    private function autorizarMascota(int $mascotaId): void
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        $ok = Mascota::whereHas('cliente', fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereKey($mascotaId)
            ->exists();

        abort_unless($ok, 404);
    }

    public function pdf(Receta $receta)
    {
        // Seguridad: la receta debe pertenecer a la clínica del usuario
        $tenantId = auth()->user()->tenant_id ?? null;
        if ($receta->mascota && $receta->mascota->cliente) {
            abort_unless($receta->mascota->cliente->tenant_id == $tenantId, 404);
        }

        // Cargar relaciones que la vista del PDF necesita
        $receta->load(['mascota.cliente', 'veterinario', 'medicamentos']);

        $pdf = Pdf::loadView('pdf-receta', ['receta' => $receta])
            ->setPaper('letter');

        $nombreArchivo = 'receta-'
            . ($receta->mascota->nombre ?? 'mascota')
            . '-'
            . optional($receta->fecha_emision)->format('Y-m-d')
            . '.pdf';

        return $pdf->download($nombreArchivo);
    }
        public function pdfPreview(Receta $receta)
    {
        // Seguridad
        $tenantId = auth()->user()->tenant_id ?? null;
        if ($receta->mascota && $receta->mascota->cliente) {
            abort_unless($receta->mascota->cliente->tenant_id == $tenantId, 404);
        }

        $receta->load(['mascota.cliente', 'veterinario', 'medicamentos']);

        $pdf = Pdf::loadView('pdf-receta', ['receta' => $receta])
            ->setPaper('letter');

        // stream() = inline en el navegador; download() = descarga
        return $pdf->stream('receta-' . $receta->id . '.pdf');
    }
}