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
        'subtotal',
        'iva_valor',
        'total',
        'estado',        // pendiente | recibida | anulada
        'observacion',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'subtotal'     => 'decimal:2',
        'iva_valor'    => 'decimal:2',
        'total'        => 'decimal:2',
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
