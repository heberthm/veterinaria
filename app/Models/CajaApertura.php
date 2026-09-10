<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CajaApertura extends Model
{
    use HasFactory;

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'saldo_inicial',
        'saldo_final',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_final' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    const ESTADOS = [
        'abierta' => 'Abierta',
        'cerrada' => 'Cerrada',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }
}