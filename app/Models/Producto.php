<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'categoria_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo',            // producto | servicio | paquete
        'precio_compra',
        'precio_venta',
        'iva_porcentaje',  // 0 | 5 | 19 (Colombia)
        'stock',
        'stock_minimo',
        'unidad_medida',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'precio_compra'   => 'decimal:2',
        'precio_venta'    => 'decimal:2',
        'iva_porcentaje'  => 'decimal:2',
        'activo'          => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function precioConIva(): float
    {
        return round($this->precio_venta * (1 + ($this->iva_porcentaje / 100)), 2);
    }
}
