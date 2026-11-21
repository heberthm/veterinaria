<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'proveedor_id',
        'user_id',
        'numero_factura_proveedor',
        'fecha_compra',
        'fecha_vencimiento', // 🔥 Agregar si no lo tenías en fillable
        'metodo_pago',       // 🔥 Agregar si no lo tenías en fillable
        'subtotal',
        'iva_valor',
        'total',
        'items_json', 
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha_compra'      => 'date',
        'fecha_vencimiento' => 'date',
        'total'             => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'iva_valor'         => 'decimal:2', // Opcional, pero recomendado
        'items_json'        => 'array',     // 🔥 ESTA ES LA LÍNEA QUE FALTA
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function items()
    {
        return $this->hasMany(CompraItem::class);
    }
}