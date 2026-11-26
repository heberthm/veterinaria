<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $rep['titulo'] }}</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10px; color: #1e293b; margin: 0; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        .cab td { vertical-align: top; }
        .resumen { margin-top: 12px; }
        .resumen td { border: 1px solid #cbd5e1; padding: 6px 8px; width: 20%; }
        .resumen .lbl { color: #64748b; font-size: 9px; }
        .resumen .val { font-size: 13px; font-weight: bold; margin-top: 2px; }
        .datos { margin-top: 14px; }
        .datos th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 9.5px; text-align: left; }
        .datos td { border: 1px solid #e2e8f0; padding: 5px 6px; }
        .datos tr.total td { border-top: 2px solid #1e293b; font-weight: bold; background: #f8fafc; }
        .pie { margin-top: 18px; text-align: center; color: #64748b; font-size: 9px; }
    </style>
</head>
<body>

    <table class="cab">
        <tr>
            <td style="width: 30%;">
                <div style="font-size: 16px; font-weight: bold;">{{ $rep['titulo'] }}</div>
            </td>
            <td style="width: 40%;" class="center">
                <div style="font-size: 24px; font-weight: bold; color: #0f172a;">{{ $empresa }}</div>
                <div class="muted">Clínica Veterinaria</div>
            </td>
            <td style="width: 30%;" class="right">
                @if (!$sinFechas)
                    <div style="font-weight: bold;">{{ $desde }} - {{ $hasta }}</div>
                @else
                    <div style="font-weight: bold;">Estado actual</div>
                @endif
                <div class="muted">Generado: {{ $generado }}</div>
                @if ($usuario)
                    <div class="muted">Por: {{ $usuario }}</div>
                @endif
            </td>
        </tr>
    </table>

    @if (!empty($fm['resumen']))
        <table class="resumen">
            <tr>
                @foreach ($fm['resumen'] as $r)
                    <td>
                        <div class="lbl">{{ $r['label'] }}</div>
                        <div class="val">{{ $r['valor'] }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    @if (!empty($rep['aviso']))
        <p class="muted" style="margin-top: 14px;">{{ $rep['aviso'] }}</p>
    @endif

    @if (!empty($rep['columnas']))
        <table class="datos">
            <thead>
                <tr>
                    @foreach ($rep['columnas'] as $c)
                        <th class="{{ in_array($c['t'] ?? '', ['moneda', 'numero', 'porcentaje']) ? 'right' : '' }}">{{ $c['l'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($fm['filas'] as $f)
                    <tr>
                        @foreach ($rep['columnas'] as $c)
                            <td class="{{ in_array($c['t'] ?? '', ['moneda', 'numero', 'porcentaje']) ? 'right' : '' }}">{{ $f[$c['k']] }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($rep['columnas']) }}" class="center muted">No hay datos para el período seleccionado.</td></tr>
                @endforelse

                @if (!empty($fm['totales']))
                    <tr class="total">
                        @foreach ($rep['columnas'] as $i => $c)
                            <td class="{{ in_array($c['t'] ?? '', ['moneda', 'numero', 'porcentaje']) ? 'right' : '' }}">
                                {{ $i === 0 ? 'TOTAL' : ($fm['totales'][$c['k']] ?? '') }}
                            </td>
                        @endforeach
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <div class="pie">{{ $empresa }} · {{ count($rep['filas']) }} registro(s)</div>

</body>
</html>