<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #1A2332; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #64748B; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #E7EBF3; font-size: 11px; }
        .section-title { font-size: 12px; font-weight: bold; margin-top: 16px; margin-bottom: 4px; color: #2F6FED; }
        .box { background: #F8F9FC; border: 1px solid #E7EBF3; border-radius: 6px; padding: 8px 10px; margin-bottom: 8px; }
        .grid { width: 100%; }
        .grid td { border: none; vertical-align: top; padding: 0 8px 8px 0; width: 50%; }
        .header { border-bottom: 2px solid #2F6FED; padding-bottom: 10px; margin-bottom: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VetCloud — Historia Clínica</h1>
        <div class="muted">
            {{ $historia->mascota->nombre }} — {{ $historia->mascota->raza ?? $historia->mascota->especie }}
            &bull; Consulta del {{ $historia->fecha_consulta->format('d/m/Y H:i') }}
            &bull; Veterinario: {{ $historia->veterinario->name ?? 'No asignado' }}
        </div>
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="section-title">Motivo de consulta</div>
                <div class="box">{{ $historia->motivo_consulta ?: '—' }}</div>
            </td>
            <td>
                <div class="section-title">Diagnóstico</div>
                <div class="box">{{ $historia->diagnostico ?: '—' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="section-title">Anamnesis</div>
                <div class="box">{{ $historia->anamnesis ?: '—' }}</div>
            </td>
            <td>
                <div class="section-title">Tratamiento</div>
                <div class="box">{!! nl2br(e($historia->tratamiento ?: '—')) !!}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Signos vitales</div>
    <table>
        <tr>
            <th>Temperatura</th><th>Frec. cardíaca</th><th>Frec. respiratoria</th><th>Peso</th><th>Estado corporal</th><th>Hidratación</th>
        </tr>
        <tr>
            <td>{{ $historia->temperatura ?? '—' }} °C</td>
            <td>{{ $historia->frecuencia_cardiaca ?? '—' }} lpm</td>
            <td>{{ $historia->frecuencia_respiratoria ?? '—' }} rpm</td>
            <td>{{ $historia->peso ?? '—' }} kg</td>
            <td>{{ $historia->estado_corporal ?? '—' }}</td>
            <td>{{ $historia->hidratacion ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Examen físico</div>
    <div class="box">{{ $historia->examen_fisico ?: '—' }}</div>

    <div class="section-title">Observaciones</div>
    <div class="box">{{ $historia->observaciones ?: '—' }}</div>

    @if ($historia->formulaMedica->isNotEmpty())
        <div class="section-title">Fórmula médica</div>
        <table>
            <tr><th>Producto</th><th>Presentación</th><th>Dosis</th><th>Cantidad</th></tr>
            @foreach ($historia->formulaMedica as $item)
                <tr>
                    <td>{{ $item->producto }}</td>
                    <td>{{ $item->presentacion }}</td>
                    <td>{{ $item->dosis }}</td>
                    <td>{{ $item->cantidad }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <p class="muted" style="margin-top:20px;">Documento generado por VetCloud el {{ now()->format('d/m/Y H:i') }}.</p>
</body>
</html>
