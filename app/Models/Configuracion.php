<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración general de una clínica (una fila por tenant).
 *
 * Uso en cualquier parte de la app:
 *     $cfg = \App\Models\Configuracion::actual();
 *     $cfg->nombre_clinica; $cfg->iva_porcentaje; $cfg->prefijo_factura;
 */
class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'tenant_id',
        'nombre_clinica', 'razon_social', 'nit', 'direccion', 'ciudad',
        'telefono', 'email', 'sitio_web', 'logo',
        'iva_porcentaje', 'prefijo_factura', 'moneda', 'formato_impresion', 'pie_factura',
        'duracion_cita_min', 'horario',
    ];

    protected $casts = [
        'iva_porcentaje'    => 'float',
        'duracion_cita_min' => 'integer',
        'horario'           => 'array',
    ];

    public const DIAS = [
        'lunes'     => 'Lunes',
        'martes'    => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves'    => 'Jueves',
        'viernes'   => 'Viernes',
        'sabado'    => 'Sábado',
        'domingo'   => 'Domingo',
    ];

    /** Configuración de una clínica; se crea con los valores por defecto la primera vez. */
    public static function deTenant(int $tenantId): self
    {
        $cfg = static::firstOrCreate(['tenant_id' => $tenantId]);

        // Trae los valores por defecto que puso la base de datos
        return $cfg->wasRecentlyCreated ? $cfg->fresh() : $cfg;
    }

    /** Configuración de la clínica del usuario autenticado (mismo criterio de tenant que el POS). */
    public static function actual(): self
    {
        return static::deTenant((int) (auth()->user()->tenant_id ?? 1));
    }

    /** Valores por defecto del horario de atención. */
    public static function horarioPorDefecto(): array
    {
        $h = [];
        foreach (array_keys(self::DIAS) as $dia) {
            $h[$dia] = ['abierto' => true, 'desde' => '08:00', 'hasta' => '18:00'];
        }
        $h['sabado']  = ['abierto' => true,  'desde' => '08:00', 'hasta' => '13:00'];
        $h['domingo'] = ['abierto' => false, 'desde' => '08:00', 'hasta' => '12:00'];

        return $h;
    }

    /** Horario guardado combinado con los valores por defecto (nunca faltan días). */
    public function horarioCompleto(): array
    {
        $base  = self::horarioPorDefecto();
        $saved = is_array($this->horario) ? $this->horario : [];

        foreach ($base as $dia => $def) {
            if (isset($saved[$dia]) && is_array($saved[$dia])) {
                $base[$dia] = array_merge($def, array_intersect_key($saved[$dia], $def));
                $base[$dia]['abierto'] = (bool) $base[$dia]['abierto'];
            }
        }

        return $base;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        // Se sirve desde Laravel (no depende de `storage:link` ni de enlaces simbólicos en Windows)
        return route('configuracion.logo', ['v' => optional($this->updated_at)->timestamp ?? 1]);
    }

    /** ¿Existe ya la tabla? (evita errores si aún no se ejecutó la migración) */
    public static function disponible(): bool
    {
        return Schema::hasTable('configuraciones');
    }
}