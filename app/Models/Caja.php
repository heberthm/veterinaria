<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * 🔥 RELACIÓN: Usuario que abrió la caja
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_apertura_id');
    }

    /**
     * 🔥 ALIAS: Usuario de apertura
     */
    public function usuarioApertura()
    {
        return $this->belongsTo(User::class, 'usuario_apertura_id');
    }

    /**
     * 🔥 RELACIÓN: Usuario que cerró la caja
     */
    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    public function aperturas()
    {
        return $this->hasMany(CajaApertura::class);
    }

    public function movimientos()
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function aperturaActual()
    {
        return CajaApertura::where('caja_id', $this->id)
            ->where('estado', 'abierta')
            ->orderBy('fecha_apertura', 'desc')
            ->first();
    }

    public function estaAbierta()
    {
        return $this->estado === 'abierta';
    }
}