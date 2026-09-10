<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion', 
        'saldo_inicial',
        'saldo_actual',
        'estado',
        'usuario_id',
        'usuario_apertura_id',
        'usuario_cierre_id',
        'fecha_apertura',
        'fecha_cierre',
        'activa',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'activa' => 'boolean',
    ];

    const ESTADOS = [
        'abierta' => 'Abierta',
        'cerrada' => 'Cerrada',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function usuarioApertura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_apertura_id');
    }

    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    public function aperturas(): HasMany
    {
        return $this->hasMany(CajaApertura::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function aperturaActual()
    {
        return $this->aperturas()->where('estado', 'abierta')->latest()->first();
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public function saldoDisponible(): float
    {
        return $this->saldo_actual;
    }
}