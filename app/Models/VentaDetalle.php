<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'ventaDetalle';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'nombre_producto',  
        'precio_unitario',
        'cantidad',
        'iva_porcentaje',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'iva_porcentaje'  => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
