{{-- resources/views/historia-clinica-pdf.blade.php  (DomPDF: solo tablas y CSS 2.1, sin flex/grid) --}}
@php
    $m = $historia->mascota;
    $c = $m?->cliente;

    // --- Mascota ---
    $edad = $m?->edad;
    if (blank($edad) && filled($m?->fecha_nacimiento)) {
        $anios = \Carbon\Carbon::parse($m->fecha_nacimiento)->age;
        $edad  = $anios . ($anios === 1 ? ' año' : ' años');
    } elseif (is_numeric($edad)) {
        $edad = $edad . ((int) $edad === 1 ? ' año' : ' años');
    }
    $pesoMascota = $m?->peso ?? $historia->peso;
    $sexo        = strtolower((string) ($m?->sexo ?? ''));
    $estadoMasc  = strtolower((string) ($m?->estado ?? ''));
    $mostrarEstado = filled($estadoMasc) && !in_array($estadoMasc, ['activo', 'activa', 'vivo', 'viva', '1'], true);

    $subtitulo = collect([
        $m?->raza ?: $m?->especie,
        $edad,
        filled($pesoMascota) ? rtrim(rtrim(number_format((float) $pesoMascota, 2, '.', ''), '0'), '.') . ' kg' : null,
    ])->filter()->implode('  •  ');

    // --- Propietario ---
    $propietario = $c?->nombre_completo
        ?? trim(($c?->nombre ?? $c?->nombres ?? '') . ' ' . ($c?->apellido ?? $c?->apellidos ?? ''));

    // --- Fecha de la consulta ---
    $f   = $historia->fecha_consulta;
    $mes = $f ? ucfirst($f->copy()->locale('es')->translatedFormat('F')) : '';
    $tituloFecha = $f ? $f->format('j') . ' de ' . $mes . ' de ' . $f->format('Y') . ' - ' . $f->format('h:i A') : '—';

    // --- Tratamiento (puede ser texto o array) ---
    $trat = $historia->tratamiento;
    if (is_array($trat)) {
        $trat = implode("\n", array_map(
            fn ($t) => is_array($t) ? implode(' · ', array_filter($t)) : $t,
            $trat
        ));
    }

    // --- Signos vitales ---
    $num = fn ($v, $d = 2) => filled($v) ? number_format((float) $v, $d, '.', '') : '—';

    // --- Recetas de la consulta (relación nueva) y fórmula antigua (si existe) ---
    $recetas = $historia->relationLoaded('recetas') ? $historia->recetas : collect();
    $formula = $historia->relationLoaded('formulaMedica') ? $historia->formulaMedica : collect();

    $esImagen = fn ($a) => in_array(strtolower(pathinfo((string) $a->ruta, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    $archivos = $historia->relationLoaded('archivos') ? $historia->archivos : collect();

    $pawSvg = 'data:image/svg+xml;base64,' . base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
        . '<g fill="#3F4B5B">'
        . '<ellipse cx="14" cy="28" rx="7" ry="9"/><ellipse cx="27" cy="16" rx="7" ry="10"/>'
        . '<ellipse cx="42" cy="16" rx="7" ry="10"/><ellipse cx="54" cy="28" rx="7" ry="9"/>'
        . '<path d="M32 30c-10 0-19 11-19 19 0 6 5 9 11 8 3-.5 5-1.5 8-1.5s5 1 8 1.5c6 1 11-2 11-8 0-8-9-19-19-19z"/>'
        . '</g></svg>'
    );
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>hc_{{ \Illuminate\Support\Str::slug($m?->nombre ?? 'historia', '_') }}</title>
    <style>
        @page { margin: 26px 30px; }
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #1A2332; margin: 0; }

        .card { border: 1px solid #E3E8F0; border-radius: 10px; padding: 14px 18px; margin-bottom: 14px; }
        table { border-collapse: collapse; }
        .w100 { width: 100%; }

        /* Encabezado: mascota | título | propietario */
        .head td { vertical-align: middle; }
        .pet-name { font-size: 17px; font-weight: bold; color: #1A2332; }
        .sexo { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; font-weight: bold; }
        .sexo-h { color: #B0306A; }
        .sexo-m { color: #2F6FED; }
        .sub { color: #475569; font-size: 11px; }
        .estado { color: #94A3B8; font-size: 11px; margin-left: 10px; }
        .titulo { text-align: center; font-size: 17px; font-weight: bold; color: #1A2332; letter-spacing: .3px; }
        .owner-lbl { color: #475569; font-size: 10.5px; }
        .owner-name { font-size: 12px; margin-top: 4px; color: #1A2332; }

        /* Cuerpo */
        .consulta { font-size: 14px; font-weight: bold; margin: 0 0 12px 0; }
        .lbl { font-size: 11px; font-weight: bold; margin: 12px 0 5px 0; color: #1A2332; }
        .lbl-n { font-size: 11px; margin: 12px 0 5px 0; color: #1A2332; }
        .box { border: 1px solid #E3E8F0; border-radius: 6px; padding: 8px 10px; font-size: 11px; line-height: 1.35; }
        .h-s { min-height: 16px; }
        .h-m { min-height: 40px; }
        .h-l { min-height: 52px; }

        /* Signos vitales */
        .vit td { padding: 0 8px 0 0; vertical-align: top; }
        .vit-l { font-size: 9.5px; color: #475569; margin-bottom: 3px; }
        .vit-v { border: 1px solid #E3E8F0; border-radius: 5px; padding: 5px 6px; font-size: 10.5px; font-weight: bold; text-align: center; width: 66px; }
        .vit-v span { font-weight: normal; color: #64748B; font-size: 9px; }

        /* Receta */
        .rx { border: 1px solid #E3E8F0; border-radius: 8px; padding: 9px 12px; margin-bottom: 6px; }
        .rx-title { font-size: 12px; font-weight: bold; }
        .rx-med { font-size: 11px; margin-top: 6px; }
        .rx-med small { display: block; font-size: 9px; color: #475569; margin-top: 1px; }
        .rx-ind { margin-top: 6px; font-size: 10.5px; }
        .activa { color: #16A34A; font-size: 10.5px; text-align: right; }
        .vencida { color: #94A3B8; font-size: 10.5px; text-align: right; }

        .frm th, .frm td { text-align: left; padding: 5px 8px; border-bottom: 1px solid #E3E8F0; font-size: 10.5px; }

        /* Adjuntos */
        .adj-img { width: 130px; height: 104px; border: 1px solid #E3E8F0; border-radius: 6px; }
        .adj-doc { display: inline-block; border: 1px solid #E3E8F0; border-radius: 6px; padding: 8px 10px; font-size: 10px; margin: 0 8px 8px 0; }

        .foot { color: #94A3B8; font-size: 9.5px; text-align: center; margin-top: 10px; }
        .keep { page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- ENCABEZADO: nombre de la mascota · HISTORIA CLÍNICA · propietario --}}
    <div class="card" style="padding: 12px 18px;">
        <table class="w100 head">
            <tr>
                <td style="width: 46px;">
                    <img src="{{ $pawSvg }}" width="30" height="30" alt="">
                </td>
                <td style="width: 34%;">
                    <div>
                        <span class="pet-name">{{ $m?->nombre ?? '—' }}</span>
                        @if (in_array($sexo, ['hembra', 'h', 'f', 'femenino'], true))
                            <span class="sexo sexo-h">&#9792;</span>
                        @elseif (in_array($sexo, ['macho', 'm', 'masculino'], true))
                            <span class="sexo sexo-m">&#9794;</span>
                        @endif
                    </div>
                    <div class="sub" style="margin-top: 4px;">
                        {{ $subtitulo ?: '—' }}
                        @if ($mostrarEstado)
                            <span class="estado">{{ ucfirst($estadoMasc) }}</span>
                        @endif
                    </div>
                </td>
                <td class="titulo" style="width: 26%;">Historia clínica</td>
                <td style="width: 34%; text-align: right;">
                    <div class="owner-lbl">Propietario</div>
                    <div class="owner-name">{{ $propietario ?: '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- CONSULTA --}}
    <div class="card">
        <div class="consulta">Consulta del {{ $tituloFecha }}</div>

        <div class="lbl" style="margin-top: 0;">Motivo de consulta</div>
        <div class="box h-s">{{ $historia->motivo_consulta ?: '—' }}</div>

        <div class="lbl">Anamnesis</div>
        <div class="box h-m">{!! nl2br(e($historia->anamnesis ?: '—')) !!}</div>

        <div class="lbl-n">Signos Vitales</div>
        <table class="vit">
            <tr>
                <td><div class="vit-l">Temp.</div><div class="vit-v">{{ $num($historia->temperatura) }} <span>°C</span></div></td>
                <td><div class="vit-l">F. card.</div><div class="vit-v">{{ filled($historia->frecuencia_cardiaca) ? $historia->frecuencia_cardiaca : '—' }} <span>lpm</span></div></td>
                <td><div class="vit-l">F. resp.</div><div class="vit-v">{{ filled($historia->frecuencia_respiratoria) ? $historia->frecuencia_respiratoria : '—' }} <span>rpm</span></div></td>
                <td><div class="vit-l">Peso</div><div class="vit-v">{{ $num($historia->peso) }} <span>kg</span></div></td>
                <td><div class="vit-l">Est. corporal</div><div class="vit-v">{{ $historia->estado_corporal ?: '—' }}</div></td>
                <td><div class="vit-l">Hidratación</div><div class="vit-v">{{ $historia->hidratacion ?: '—' }}</div></td>
            </tr>
        </table>

        <div class="lbl">Examen Físico</div>
        <div class="box h-m">{!! nl2br(e($historia->examen_fisico ?: '—')) !!}</div>

        <div class="lbl">Diagnóstico</div>
        <div class="box h-s">{!! nl2br(e($historia->diagnostico ?: '—')) !!}</div>

        <div class="lbl">Tratamiento</div>
        <div class="box h-l">{!! nl2br(e($trat ?: '—')) !!}</div>

        <div class="lbl-n">Fórmula Médica</div>

        @forelse ($recetas as $r)
            <div class="rx keep">
                <table class="w100">
                    <tr>
                        <td>
                            <div class="rx-title">Receta del {{ optional($r->fecha_emision)->format('d/m/Y') }}</div>
                            <div class="sub" style="margin-top: 2px;">{{ $r->veterinario->name ?? '—' }}</div>
                        </td>
                        <td style="width: 70px;" class="{{ ($r->estado ?? '') === 'activa' ? 'activa' : 'vencida' }}">
                            {{ ($r->estado ?? '') === 'activa' ? 'Activa' : 'Vencida' }}
                        </td>
                    </tr>
                </table>

                @foreach ($r->medicamentos as $med)
                    <div class="rx-med">
                        {{ $med->medicamento }} — {{ $med->dosis }}
                        @if ($med->frecuencia) {{ $med->frecuencia }} @endif
                        @if ($med->duracion_dias) &middot; {{ $med->duracion_dias }} días @endif
                        @if ($med->instrucciones) <small>{{ $med->instrucciones }}</small> @endif
                    </div>
                @endforeach

                @if ($r->indicaciones_generales)
                    <div class="rx-ind">{{ $r->indicaciones_generales }}</div>
                @endif
            </div>
        @empty
            @if ($formula->isNotEmpty())
                <table class="w100 frm">
                    <tr><th>Producto</th><th>Presentación</th><th>Dosis</th><th>Cantidad</th></tr>
                    @foreach ($formula as $item)
                        <tr>
                            <td>{{ $item->producto }}</td>
                            <td>{{ $item->presentacion }}</td>
                            <td>{{ $item->dosis }}</td>
                            <td>{{ $item->cantidad }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="box h-s" style="color:#64748B;">Sin recetas en esta consulta.</div>
            @endif
        @endforelse

        <div class="lbl">Observaciones</div>
        <div class="box h-s">{!! nl2br(e($historia->observaciones ?: '—')) !!}</div>

        <div class="lbl">Próxima cita sugerida</div>
        <div class="box h-s">
            {{ $historia->proxima_cita_sugerida ? $historia->proxima_cita_sugerida->format('d/m/Y') : '—' }}
        </div>

        @if ($archivos->isNotEmpty())
            <div class="lbl">Archivos / Adjuntos</div>
            @foreach ($archivos as $a)
                @php $ruta = storage_path('app/public/' . $a->ruta); @endphp
                @if ($esImagen($a) && is_file($ruta))
                    <img class="adj-img" src="{{ $ruta }}" alt="{{ $a->nombre_original }}" style="margin: 0 8px 8px 0;">
                @else
                    <div class="adj-doc">{{ $a->nombre_original }}</div>
                @endif
            @endforeach
        @endif
    </div>

    <div class="foot">Documento generado por hemTech el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>