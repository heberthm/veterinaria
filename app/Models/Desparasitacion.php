<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesparasitacionAplicacion extends Model
{
    use HasFactory;

    protected $table = 'desparasitacion_aplicaciones';

    protected $fillable = [
        'desparasitante_id',
        'mascota_id',
        'usuario_id',
        'fecha_aplicacion',
        'fecha_proxima',
        'dosis_aplicada',
        'lote',
        'via_aplicacion',
        'observaciones',
        'costo',
        'estado',
    ];

    protected $casts = [
        'fecha_aplicacion' => 'date',
        'fecha_proxima' => 'date',
        'dosis_aplicada' => 'decimal:2',
        'costo' => 'decimal:2',
    ];

    const ESTADOS = [
        'aplicada' => 'Aplicada',
        'programada' => 'Programada',
        'cancelada' => 'Cancelada',
    ];

    public function desparasitante(): BelongsTo
    {
        return $this->belongsTo(Desparasitante::class);
    }

    public function mascota(): BelongsTo
    {
        return $this->belongsTo(Mascota::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}