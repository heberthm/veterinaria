<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'mascota_id',
        'usuario_id', 
        'fecha',
        'total',
        'subtotal',
        'iva',
        'descuento',
        'metodo_pago',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    // 🔥 RELACIÓN CON USUARIO - DEBE SER 'usuario_id'
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}