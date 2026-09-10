<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'venta_id',
        'numero_factura',
        'fecha_emision',
        'fecha_vencimiento',
        'tipo',
        'estado',
        'subtotal',
        'iva',
        'descuento',
        'total',
        'abonado',
        'saldo',
        'observaciones',
        'metodo_pago',
        'referencia_pago',
        'fecha_pago',
        'usuario_id',
        'configuracion',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'abonado' => 'decimal:2',
        'saldo' => 'decimal:2',
        'configuracion' => 'array',
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'pagada' => 'Pagada',
        'anulada' => 'Anulada',
        'vencida' => 'Vencida',
    ];

    const TIPOS = [
        'venta' => 'Venta',
        'servicio' => 'Servicio',
        'consulta' => 'Consulta',
        'proforma' => 'Proforma',
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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(FacturaPago::class);
    }

    /**
     * Generar número de factura automático
     */
    public static function generarNumeroFactura()
    {
        $ultima = self::orderBy('id', 'desc')->first();
        $numero = $ultima ? intval(substr($ultima->numero_factura, -6)) + 1 : 1;
        return 'F' . date('Y') . '-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verificar si la factura está pagada
     */
    public function estaPagada(): bool
    {
        return $this->estado === 'pagada';
    }

    /**
     * Verificar si la factura está pendiente
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si la factura está vencida
     */
    public function estaVencida(): bool
    {
        return $this->estado === 'vencida';
    }

    /**
     * Calcular el saldo pendiente
     */
    public function calcularSaldo(): float
    {
        return max(0, $this->total - $this->abonado);
    }

    /**
     * Actualizar el saldo de la factura
     */
    public function actualizarSaldo()
    {
        $this->saldo = $this->calcularSaldo();
        $this->save();
    }

    /**
     * Registrar un pago
     */
    public function registrarPago($monto, $metodoPago, $referencia = null, $observaciones = null)
    {
        $pago = FacturaPago::create([
            'factura_id' => $this->id,
            'fecha_pago' => now(),
            'monto' => $monto,
            'metodo_pago' => $metodoPago,
            'referencia' => $referencia,
            'observaciones' => $observaciones,
            'usuario_id' => auth()->id(),
        ]);

        $this->abonado += $monto;
        $this->saldo = $this->calcularSaldo();

        if ($this->saldo <= 0) {
            $this->estado = 'pagada';
            $this->fecha_pago = now();
        }

        $this->save();

        return $pago;
    }
}