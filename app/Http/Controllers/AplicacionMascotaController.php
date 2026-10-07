<?php

namespace App\Http\Controllers;

use App\Models\Desparasitante;
use App\Models\DesparasitacionAplicacion;
use App\Models\Mascota;
use App\Models\Vacuna;
use App\Models\VacunaAplicacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AplicacionMascotaController extends Controller
{
    /** Unidades de stock que descuenta cada aplicación. */
    private const UNIDADES_POR_APLICACION = 1;

    public function storeVacuna(Request $request, Mascota $mascota)
    {
        return $this->registrar(
            $request, $mascota,
            Vacuna::class, VacunaAplicacion::class,
            'vacuna_id', 'vacunas', 'Vacuna registrada.'
        );
    }

    public function storeDesparasitacion(Request $request, Mascota $mascota)
    {
        return $this->registrar(
            $request, $mascota,
            Desparasitante::class, DesparasitacionAplicacion::class,
            'desparasitante_id', 'desparasitantes', 'Desparasitación registrada.'
        );
    }

    public function destroyVacuna(VacunaAplicacion $aplicacion)
    {
        return $this->eliminar($aplicacion, Vacuna::class, 'vacuna_id');
    }

    public function destroyDesparasitacion(DesparasitacionAplicacion $aplicacion)
    {
        return $this->eliminar($aplicacion, Desparasitante::class, 'desparasitante_id');
    }

    // ------------------------------------------------------------------

    private function registrar(
        Request $request,
        Mascota $mascota,
        string $producto,       // Vacuna::class | Desparasitante::class
        string $aplicacion,     // VacunaAplicacion::class | DesparasitacionAplicacion::class
        string $fk,             // vacuna_id | desparasitante_id
        string $tabla,          // vacunas | desparasitantes
        string $mensaje
    ) {
        $this->autorizarMascota($mascota->id);

        $data     = $request->validate($this->reglas($tabla));
        $tenantId = auth()->user()->tenant_id ?? null;

        $aviso = DB::transaction(function () use ($data, $mascota, $producto, $aplicacion, $fk, $tenantId) {
            // Bloquea la fila del producto para que aplicaciones simultáneas no pisen el stock
            $item  = $producto::where('tenant_id', $tenantId)->lockForUpdate()->findOrFail($data['producto_id']);
            $aviso = null;

            if ($data['estado'] === 'aplicada') {
                $caduca = $item->fecha_caducidad;

                if ($caduca && $caduca->lt(Carbon::parse($data['fecha_aplicacion']))) {
                    throw ValidationException::withMessages([
                        'producto_id' => 'El producto "' . $item->nombre . '" está vencido (caducó el ' . $caduca->format('d/m/Y') . ').',
                    ]);
                }

                if ($item->stock < self::UNIDADES_POR_APLICACION) {
                    throw ValidationException::withMessages([
                        'producto_id' => 'Sin stock disponible de "' . $item->nombre . '". Actualiza el inventario primero.',
                    ]);
                }

                $item->decrement('stock', self::UNIDADES_POR_APLICACION);

                if ($item->necesitaStock()) {
                    $aviso = 'Stock bajo de "' . $item->nombre . '": quedan ' . $item->stock . '.';
                }
            }

            $aplicacion::create(
                Arr::except($data, ['producto_id']) + [
                    $fk          => $item->id,
                    'mascota_id' => $mascota->id,
                    'usuario_id' => auth()->id(),
                    'costo'      => $data['costo'] ?? 0,
                ]
            );

            return $aviso;
        });

        return response()->json(['message' => $mensaje, 'aviso' => $aviso]);
    }

    private function eliminar($registro, string $producto, string $fk)
    {
        $this->autorizarMascota($registro->mascota_id);

        DB::transaction(function () use ($registro, $producto, $fk) {
            // Si estaba aplicada, la unidad vuelve al inventario
            if ($registro->estado === 'aplicada') {
                $item = $producto::lockForUpdate()->find($registro->{$fk});
                if ($item) {
                    $item->increment('stock', self::UNIDADES_POR_APLICACION);
                }
            }

            $registro->delete();
        });

        return response()->json(['message' => 'Registro eliminado.']);
    }

    /** Reglas comunes; el producto debe pertenecer al catálogo de la clínica. */
    private function reglas(string $tablaCatalogo): array
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        return [
            'producto_id'      => ['required', Rule::exists($tablaCatalogo, 'id')->where('tenant_id', $tenantId)],
            'fecha_aplicacion' => 'required|date',
            'fecha_proxima'    => 'nullable|date|after:fecha_aplicacion',
            'dosis_aplicada'   => 'nullable|numeric|min:0',
            'lote'             => 'nullable|string|max:50',
            'via_aplicacion'   => 'nullable|string|max:50',
            'costo'            => 'nullable|numeric|min:0',
            'estado'           => 'required|in:aplicada,programada,cancelada',
            'observaciones'    => 'nullable|string',
        ];
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
}