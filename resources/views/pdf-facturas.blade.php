<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $venta->numero_factura ?? $venta->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        /* ---- Encabezado ---- */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
        }
        .titulo {
            font-size: 26px;
            font-weight: normal;
            margin: 0 0 5px 0;
        }
        .subtitulo {
            font-size: 11px;
            margin: 2px 0;
        }
        .factura-text {
            text-align: right;
        }
        .factura-text h2 {
            font-size: 26px;
            font-weight: normal;
            margin: 0 0 8px 0;
        }
        .factura-text p {
            margin: 3px 0;
            font-size: 11px;
        }

        /* ---- Sección Cliente / Vendedor / Pago ---- */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .info-table td {
            vertical-align: top;
            width: 33.33%;
            padding-top: 10px;
        }
        .info-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .info-table p {
            margin: 3px 0;
            font-size: 11px;
        }

        /* ---- Tabla de Productos ---- */
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .productos-table th {
            background-color: #0E1B30;
            color: #ffffff;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px;
            border: 1px solid #0E1B30;
            text-align: center;
        }
        .productos-table td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* ---- Totales ---- */
        .totales-table {
            width: 250px;
            float: right;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totales-table td {
            padding: 5px 10px;
            font-size: 11px;
            border: none;
        }
        .totales-table .label { text-align: left; }
        .totales-table .value { text-align: right; }
        .total-final td {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 8px;
        }

        /* ---- Footer ---- */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            clear: both;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO CON TABLA -->
    <table class="header-table">
        <tr>
            <td width="50%" style="text-align: left;">
                <div class="titulo">VETCLOUD</div>
                <div class="subtitulo">NIT: 000.000.000-0</div>
                <div class="subtitulo">Calle 00 #00-00 · Ciudad</div>
                <div class="subtitulo">Tel: (000) 000-0000</div>
            </td>
            <td width="50%" class="factura-text">
                <h2>FACTURA</h2>
                <p><strong>No.</strong> {{ $venta->numero_factura ?? $venta->id }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha ?? $venta->created_at)->format('d/m/Y h:i a') }}</p>
                <p><strong>Estado:</strong> {{ strtoupper($venta->estado ?? 'PAGADA') }}</p>
            </td>
        </tr>
    </table>

    <!-- SECCIÓN DE INFORMACIÓN CON TABLA -->
    <table class="info-table">
        <tr>
            <td>
                <div class="info-title">Cliente</div>
                <p><strong>Nombre:</strong> {{ $venta->cliente ? $venta->cliente->nombres . ' ' . $venta->cliente->apellidos : 'Consumidor Final' }}</p>
                @if($venta->cliente && $venta->cliente->numero_documento)
                    <p><strong>Cédula:</strong> {{ $venta->cliente->numero_documento }}</p>
                @endif
            </td>
            <td>
                <div class="info-title">Vendedor</div>
                <p><strong>Nombre:</strong> {{ $venta->usuario->name ?? '—' }}</p>
            </td>
            <td>
                <div class="info-title">Pago</div>
                <p><strong>Método:</strong> {{ strtoupper($venta->metodo_pago ?? 'EFECTIVO') }}</p>
            </td>
        </tr>
    </table>

    <!-- TABLA DE PRODUCTOS -->
    <table class="productos-table">
        <thead>
            <tr>
                <th style="text-align: left;">Descripción</th>
                <th width="60">Cant.</th>
                <th width="90">Vr. Unit.</th>
                <th width="90">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->nombre_producto ?? ($detalle->producto->nombre ?? 'Producto') }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">$ {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                    <td class="text-right">$ {{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <table class="totales-table">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">$ {{ number_format($venta->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Valor IVA</td>
            <td class="value">$ {{ number_format($venta->iva ?? 0, 0, ',', '.') }}</td>
        </tr>
        @if($venta->descuento > 0)
            <tr>
                <td class="label">Descuento</td>
                <td class="value">- $ {{ number_format($venta->descuento, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="total-final">
            <td class="label">TOTAL</td>
            <td class="value">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        Gracias por preferirnos · Documento generado por VetCloud POS
        @if($venta->usuario)
            <br>Atendido por: {{ $venta->usuario->name }}
        @endif
    </div>

</body>
</html>