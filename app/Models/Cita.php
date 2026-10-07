<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'citas';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'usuario_id',
        'mascota_id',
        'veterinario_id',
        'sede_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
        'tipo_consulta',   // Consulta general | Vacunación | Desparasitación | Control | Cirugía...
        'estado',          // pendiente | confirmada | en_atencion | finalizada | cancelada
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($cita) {
            // 🔥 Auto-asignar tenant
            if (empty($cita->tenant_id) && auth()->check()) {
                $cita->tenant_id = auth()->user()->tenant_id;
            }

            // 🔥 Auto-asignar usuario logueado
            if (empty($cita->usuario_id) && auth()->check()) {
                $cita->usuario_id = auth()->id();
            }

            // 🔥 Auto-heredar cliente_id desde la mascota
            if (empty($cita->cliente_id) && $cita->mascota_id) {
                $mascota = \App\Models\Mascota::find($cita->mascota_id);
                if ($mascota) {
                    $cita->cliente_id = $mascota->cliente_id;
                }
            }
        });
    }

    public function cliente()
        {
            return $this->belongsTo(Cliente::class);
        }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function sede()  { return $this->belongsTo(Sede::class); }

    public function scopeDelDia($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function badgeColor(): string
    {
        return match ($this->estado) {
            'confirmada'  => 'success',
            'pendiente'   => 'warning',
            'en_atencion' => 'info',
            'finalizada'  => 'purple',
            'cancelada'   => 'danger',
            default       => 'secondary',
        };
    }
}