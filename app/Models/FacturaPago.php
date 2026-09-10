<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'fecha_pago',
        'monto',
        'metodo_pago',
        'referencia',
        'observaciones',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    const METODOS_PAGO = [
        'efectivo' => 'Efectivo',
        'tarjeta_credito' => 'Tarjeta de Crédito',
        'tarjeta_debito' => 'Tarjeta Débito',
        'transferencia' => 'Transferencia Bancaria',
        'nequi' => 'Nequi',
        'daviplata' => 'DaviPlata',
        'qr' => 'Pago QR',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}