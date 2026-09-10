<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaMovimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'caja_id',
        'caja_apertura_id',
        'usuario_id',
        'venta_id',
        'factura_id',
        'tipo',
        'categoria',
        'monto',
        'saldo_anterior',
        'saldo_nuevo',
        'metodo_pago',
        'referencia',
        'descripcion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_nuevo' => 'decimal:2',
    ];

    const TIPOS = [
        'ingreso' => 'Ingreso',
        'egreso' => 'Egreso',
    ];

    const CATEGORIAS = [
        'venta' => 'Venta',
        'factura' => 'Factura',
        'abono' => 'Abono',
        'gasto' => 'Gasto',
        'retiro' => 'Retiro',
        'ajuste' => 'Ajuste',
        'apertura' => 'Apertura',
        'cierre' => 'Cierre',
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

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function apertura(): BelongsTo
    {
        return $this->belongsTo(CajaApertura::class, 'caja_apertura_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function esIngreso(): bool
    {
        return $this->tipo === 'ingreso';
    }

    public function esEgreso(): bool
    {
        return $this->tipo === 'egreso';
    }
}