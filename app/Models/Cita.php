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

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

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
