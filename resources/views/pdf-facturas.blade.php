<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura {{ $numero }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #1e293b; margin: 0; }
        h1 { font-size: 20px; margin: 0; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        .cab td { vertical-align: top; }
        .caja { border: 1px solid #cbd5e1; padding: 8px 10px; }
        .caja p { margin: 2px 0; }
        .lbl { color: #64748b; display: inline-block; width: 70px; }
        .items { margin-top: 14px; }
        .items th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; font-size: 10.5px; }
        .items td { border: 1px solid #e2e8f0; padding: 6px; }
        .totales { width: 45%; margin-left: 55%; margin-top: 10px; }
        .totales td { padding: 4px 6px; }
        .totales .fin td { border-top: 2px solid #1e293b; font-weight: bold; font-size: 13px; }
        .estado { display: inline-block; padding: 2px 8px; border: 1px solid #16a34a; color: #16a34a; font-size: 10px; font-weight: bold; }
        .estado.anulada { border-color: #dc2626; color: #dc2626; }
        .estado.pendiente { border-color: #d97706; color: #d97706; }
        .pie { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>

    <table class="cab">
        <tr>
            <td style="width: 30%;">
                <div style="font-size: 18px; font-weight: bold;">Factura de venta</div>
            </td>
            <td style="width: 40%;" class="center">
                <div style="font-size: 26px; font-weight: bold; color: #0f172a;">VetFlow</div>
                <div class="muted">Clínica Veterinaria</div>
            </td>
            <td style="width: 30%;" class="right">
                <div style="font-size: 16px; font-weight: bold;">N° {{ $numero }}</div>
                <div class="muted">{{ $fecha }}</div>
                <div style="margin-top: 4px;"><span class="estado {{ $estado }}">{{ strtoupper($estado ?: '—') }}</span></div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 14px;">
        <tr>
            <td style="width: 49%; vertical-align: top;" class="caja">
                <p><strong>Cliente</strong></p>
                <p><span class="lbl">Nombre:</span> {{ $cliente ? trim(($cliente->nombres ?? '') . ' ' . ($cliente->apellidos ?? '')) : 'Consumidor final' }}</p>
                <p><span class="lbl">Cédula:</span> {{ $cliente->numero_documento ?? '—' }}</p>
                <p><span class="lbl">Tel:</span> {{ $cliente->celular ?? '—' }}</p>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%; vertical-align: top;" class="caja">
                <p><strong>Venta</strong></p>
                <p><span class="lbl">Vendedor:</span> {{ $vendedor }}</p>
                <p><span class="lbl">Pago:</span> {{ $metodo }}</p>
                <p><span class="lbl">Estado:</span> {{ ucfirst($estado ?: '—') }}</p>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width: 8%;">Cant.</th>
                <th>Descripción</th>
                <th class="right" style="width: 20%;">Vr. Unit.</th>
                <th class="right" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lineas as $l)
                <tr>
                    <td class="center">{{ rtrim(rtrim(number_format($l['cantidad'], 2, ',', '.'), '0'), ',') }}</td>
                    <td>{{ $l['descripcion'] }}</td>
                    <td class="right">$ {{ number_format($l['precio_unitario'], 0, ',', '.') }}</td>
                    <td class="right">$ {{ number_format($l['subtotal'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="center muted">Sin productos</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totales">
        <tr><td>Subtotal</td><td class="right">$ {{ number_format($subtotal, 0, ',', '.') }}</td></tr>
        @if ($descuento > 0)
            <tr><td>Descuento</td><td class="right">- $ {{ number_format($descuento, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td>IVA</td><td class="right">$ {{ number_format($iva, 0, ',', '.') }}</td></tr>
        <tr class="fin"><td>Total</td><td class="right">$ {{ number_format($total, 0, ',', '.') }}</td></tr>
    </table>

    @if (!empty($venta->observacion) || !empty($venta->observaciones))
        <p class="muted" style="margin-top: 14px;"><strong>Observaciones:</strong> {{ $venta->observacion ?? $venta->observaciones }}</p>
    @endif

    <div class="pie">Gracias por su compra</div>

</body>
</html>