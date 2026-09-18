<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'nombre', 'descripcion',
        'saldo_inicial', 'saldo_actual', 'estado',
        'usuario_apertura_id', 'usuario_cierre_id',
        'fecha_apertura', 'fecha_cierre', 'activa',
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

    public function usuarioApertura() 
    { 
        return $this->belongsTo(User::class, 'usuario_apertura_id'); 
    }

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

    /**
     * 🔥 RELACIÓN: Apertura actual (la más reciente con estado 'abierta')
     * Retorna el MODELO directamente, no la relación
     */
    public function aperturaActual()
    {
        return $this->hasOne(CajaApertura::class)
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();  // 🔥 ESTO ES CLAVE
    }

    /**
     * 🔥 MÉTODO ALTERNATIVO: Relación directa (con nombre que Laravel entiende)
     */
    public function aperturaActualRelation()
    {
        return $this->hasOne(CajaApertura::class)
            ->where('estado', 'abierta')
            ->latest('fecha_apertura');
    }

    /**
     * Verificar si la caja está abierta
     */
    public function estaAbierta()
    {
        return $this->estado === 'abierta';
    }
}