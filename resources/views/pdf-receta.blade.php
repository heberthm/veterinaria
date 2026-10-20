<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta médica</title>
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1E293B; }
        .header { display: flex; justify-content: space-between; align-items: center;
                  border-bottom: 2px solid #2563EB; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; margin: 0; color: #2563EB; }
        .header small { color: #64748B; }
        .box { border: 1px solid #E2E8F0; border-radius: 6px; padding: 10px 12px; margin-bottom: 12px; }
        .box h3 { margin: 0 0 6px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
        th { background: #F1F5F9; text-align: left; padding: 6px 8px; border-bottom: 1px solid #E2E8F0; }
        td { padding: 6px 8px; border-bottom: 1px solid #EEF1F6; }
        .firma { margin-top: 40px; text-align: center; }
        .firma .linea { width: 220px; border-top: 1px solid #1E293B; margin: 0 auto 4px; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>{{ config('app.name', 'VetFlow') }}</h1>
            <small>Receta médica veterinaria</small>
        </div>
        <div style="text-align:right;">
            <div><b>Fecha:</b> {{ optional($receta->fecha_emision)->format('d/m/Y') }}</div>
            <div><b>Receta N°:</b> {{ $receta->id }}</div>
        </div>
    </div>

    <div class="box">
        <h3>Paciente</h3>
        <div><b>Nombre:</b> {{ $receta->mascota->nombre ?? '—' }}</div>
        <div><b>Especie:</b> {{ ucfirst($receta->mascota->especie ?? '—') }}
             · <b>Raza:</b> {{ $receta->mascota->raza ?? '—' }}</div>
        <div><b>Propietario:</b> {{ $receta->mascota->cliente->nombreCompleto() ?? '—' }}</div>
    </div>

    <div class="box">
        <h3>Medicamentos</h3>
        <table>
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Duración</th>
                    <th>Instrucciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receta->medicamentos as $m)
                    <tr>
                        <td>{{ $m->medicamento }}</td>
                        <td>{{ $m->dosis }}</td>
                        <td>{{ $m->frecuencia ?? '—' }}</td>
                        <td>{{ $m->duracion_dias ? $m->duracion_dias . ' días' : '—' }}</td>
                        <td>{{ $m->instrucciones ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($receta->indicaciones_generales)
        <div class="box">
            <h3>Indicaciones generales</h3>
            <div>{{ $receta->indicaciones_generales }}</div>
        </div>
    @endif

    @if ($receta->observaciones)
        <div class="box">
            <h3>Observaciones</h3>
            <div>{{ $receta->observaciones }}</div>
        </div>
    @endif

    <div class="firma">
        <div class="linea"></div>
        <div>{{ $receta->veterinario->name ?? '—' }}</div>
        <small>Médico veterinario</small>
    </div>

</body>
</html>