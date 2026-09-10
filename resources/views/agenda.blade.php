@extends('layouts.app')

@section('titulo', 'Agenda de Citas')

@php
    use Carbon\Carbon;

    // Ventana visible del grid: 8:00 AM a 6:00 PM, filas de 1 hora (64px c/u)
    $horaInicioGrid = 8;
    $horaFinGrid = 18;
    $pxPorHora = 64;

    $offsetCita = function ($cita) use ($horaInicioGrid, $pxPorHora) {
        $inicio = Carbon::parse($cita->hora_inicio);
        $fin = $cita->hora_fin ? Carbon::parse($cita->hora_fin) : $inicio->copy()->addMinutes(30);

        $minutosDesdeInicio = ($inicio->hour - $horaInicioGrid) * 60 + $inicio->minute;
        $duracionMinutos = max(20, $inicio->diffInMinutes($fin));

        return [
            'top'    => round(($minutosDesdeInicio / 60) * $pxPorHora),
            'height' => round(($duracionMinutos / 60) * $pxPorHora) - 4,
        ];
    };

    $iconoEstado = [
        'confirmada'  => 'fa-check',
        'pendiente'   => 'fa-pen',
        'en_atencion' => 'fa-info-circle',
        'finalizada'  => 'fa-paw',
        'cancelada'   => 'fa-times',
    ];
@endphp


@section('content_body')

<br>

    <div class="vc-page-header">
        <div class="vc-page-header__left">
            <span class="vc-page-icon"><i class="far fa-calendar"></i></span>
            <div>
                <h1>Agenda de Citas</h1>
                <p>Organiza y controla las citas de tu clínica veterinaria</p>
            </div>
        </div>
        <div class="vc-page-header__actions">
            <button type="button" class="vc-btn vc-btn-success" data-toggle="modal" data-target="#modalNuevaCita">
                <i class="fas fa-plus"></i> Nueva Cita
            </button>
            <button type="button" class="vc-btn vc-btn-outline">
                <i class="far fa-bell"></i> Recordatorios
            </button>
        </div>
    </div>

    {{-- ===================== FILTROS ===================== --}}
    <form id="formFiltrosAgenda" class="vc-filters" method="GET" action="{{ route('agenda') }}">
        <input type="hidden" name="fecha" value="{{ $fechaSeleccionada->toDateString() }}">

        <div class="vc-filter-group">
            <label>Veterinario</label>
            <select name="veterinario_id" onchange="document.getElementById('formFiltrosAgenda').submit()">
                <option value="">Todos</option>
                @foreach ($veterinarios as $vet)
                    <option value="{{ $vet->id }}" {{ request('veterinario_id') == $vet->id ? 'selected' : '' }}>
                        {{ $vet->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="vc-filter-group">
            <label>Sede</label>
            <select name="sede_id" onchange="document.getElementById('formFiltrosAgenda').submit()">
                <option value="">Principal</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>
                        {{ $sede->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="vc-filter-group">
            <label>Estado</label>
            <select name="estado" onchange="document.getElementById('formFiltrosAgenda').submit()">
                <option value="">Todos</option>
                @foreach (['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'en_atencion' => 'En atención', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'] as $valor => $etiqueta)
                    <option value="{{ $valor }}" {{ request('estado') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>

        <div class="vc-date-nav">
            <a href="{{ route('agenda', array_merge(request()->query(), ['fecha' => $fechaSeleccionada->copy()->subWeek()->toDateString()])) }}">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span><i class="far fa-calendar"></i> {{ ucfirst($fechaSeleccionada->translatedFormat('l, d \d\e F \d\e Y')) }}</span>
            <a href="{{ route('agenda', array_merge(request()->query(), ['fecha' => $fechaSeleccionada->copy()->addWeek()->toDateString()])) }}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="vc-view-toggle">
            <button type="button">Día</button>
            <button type="button" class="is-active">Semana</button>
            <button type="button">Mes</button>
        </div>
    </form>

    {{-- ===================== LAYOUT 3 COLUMNAS ===================== --}}
    <div class="vc-agenda-layout">

        {{-- Columna izquierda: mini calendario + estados + resumen --}}
        <div>
            <div class="vc-card mb-3">
                <div class="vc-card__body">
                    <div class="vc-cal-nav">
                        <button type="button"><i class="fas fa-chevron-left"></i></button>
                        <strong style="font-size:13px">{{ ucfirst($calendario['nombreMes']) }}</strong>
                        <button type="button"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="vc-cal-grid">
                        @foreach (['Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá', 'Do'] as $dow)
                            <div class="vc-cal-dow">{{ $dow }}</div>
                        @endforeach
                        @foreach ($calendario['semanas'] as $semana)
                            @foreach ($semana as $dia)
                                <a href="{{ route('agenda', array_merge(request()->query(), ['fecha' => $dia['fecha']])) }}"
                                   class="vc-cal-day {{ !$dia['esDelMes'] ? 'is-muted' : '' }} {{ $dia['esSeleccionado'] ? 'is-today' : '' }}">
                                    {{ $dia['dia'] }}
                                    @foreach ($dia['estados']->take(1) as $estado)
                                        <span class="dot dot--{{ ['confirmada' => 'green', 'pendiente' => 'orange', 'en_atencion' => 'blue', 'cancelada' => 'red'][$estado] ?? 'blue' }}"></span>
                                    @endforeach
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="vc-card mb-3">
                <div class="vc-card__header"><span class="vc-card__title">Estados de citas</span></div>
                <div class="vc-card__body">
                    @foreach ([
                        'confirmada'  => ['Confirmada', 'green'],
                        'pendiente'   => ['Pendiente', 'orange'],
                        'en_atencion' => ['En atención', 'blue'],
                        'finalizada'  => ['Finalizada', 'purple'],
                        'cancelada'   => ['Cancelada', 'red'],
                    ] as $key => [$label, $color])
                        <div class="vc-estado-row">
                            <span class="dot-label"><i style="background: var(--vc-{{ $color }})"></i>{{ $label }}</span>
                            <strong>{{ $conteoEstados[$key] ?? 0 }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="vc-card">
                <div class="vc-card__header"><span class="vc-card__title"><i class="fas fa-paw"></i> Resumen del día</span></div>
                <div class="vc-card__body">
                    <div class="vc-resumen-row">
                        <span class="label"><i class="far fa-calendar"></i> Total citas</span>
                        <strong>{{ $resumenDia['total'] }}</strong>
                    </div>
                    <div class="vc-resumen-row">
                        <span class="label"><i class="far fa-clock"></i> En atención</span>
                        <strong>{{ $resumenDia['en_atencion'] }}</strong>
                    </div>
                    <div class="vc-resumen-row">
                        <span class="label"><i class="fas fa-check"></i> Finalizadas</span>
                        <strong>{{ $resumenDia['finalizadas'] }}</strong>
                    </div>
                    <div class="vc-resumen-row">
                        <span class="label"><i class="far fa-hourglass"></i> Pendientes</span>
                        <strong>{{ $resumenDia['pendientes'] }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna central: grid semanal de horario --}}
        <div>
            <div class="vc-week-grid">
                <div class="vc-week-grid__head">
                    <div class="vc-week-col-head vc-week-time-head"></div>
                    @foreach ($diasSemana as $dia)
                        <div class="vc-week-col-head {{ $dia->isSameDay($fechaSeleccionada) ? 'is-today' : '' }}">
                            {{ $dia->translatedFormat('D') }}
                            <span>{{ $dia->day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="vc-week-body" style="height: {{ ($horaFinGrid - $horaInicioGrid) * $pxPorHora }}px">
                <div class="vc-week-times">
                    @for ($h = $horaInicioGrid; $h <= $horaFinGrid; $h++)
                        <div class="vc-week-time-slot">{{ Carbon::createFromTime($h, 0)->format('g:i A') }}</div>
                    @endfor
                </div>

                @foreach ($diasSemana as $dia)
                    <div class="vc-week-day-col" style="height: {{ ($horaFinGrid - $horaInicioGrid) * $pxPorHora }}px">
                        @foreach ($citasPorDia->get($dia->toDateString(), collect()) as $cita)
                            @php $pos = $offsetCita($cita); @endphp
                            <a href="{{ route('agenda.index', array_merge(request()->query(), ['fecha' => $dia->toDateString(), 'cita' => $cita->id])) }}"
                               class="vc-appt-card {{ $cita->estado }}"
                               style="top: {{ $pos['top'] }}px; height: {{ $pos['height'] }}px;">
                                <strong>{{ $cita->mascota->nombre }}</strong>
                                <span class="sub">{{ $cita->mascota->raza ?? $cita->mascota->especie }}</span>
                                <span class="hora">{{ Carbon::parse($cita->hora_inicio)->format('g:i A') }}</span>
                                <i class="fas state-icon {{ $iconoEstado[$cita->estado] ?? 'fa-circle' }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Columna derecha: Detalle de la Cita --}}
        <div class="vc-card vc-detail-panel">
            <div class="vc-card__header">
                <span class="vc-card__title"><i class="far fa-calendar"></i> Detalle de la Cita</span>
                @if ($citaSeleccionada)
                    <span class="vc-badge-pill {{ $citaSeleccionada->estado }}">{{ ucfirst(str_replace('_', ' ', $citaSeleccionada->estado)) }}</span>
                @endif
            </div>
            <div class="vc-card__body">
                @if ($citaSeleccionada)
                    <div class="vc-detail-pet">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($citaSeleccionada->mascota->nombre) }}&background=E8F0FE&color=2F6FED"
                             alt="{{ $citaSeleccionada->mascota->nombre }}">
                        <div>
                            <strong>{{ $citaSeleccionada->mascota->nombre }}</strong>
                            <span class="badge-tipo">{{ $citaSeleccionada->tipo_consulta ?? 'Consulta general' }}</span>
                        </div>
                    </div>

                    <div class="vc-detail-section-title">Información</div>
                    <div class="vc-detail-info-row"><i class="far fa-calendar"></i> {{ ucfirst($citaSeleccionada->fecha->translatedFormat('l, d \d\e F \d\e Y')) }}</div>
                    <div class="vc-detail-info-row"><i class="far fa-clock"></i> {{ Carbon::parse($citaSeleccionada->hora_inicio)->format('h:i A') }}@if($citaSeleccionada->hora_fin) - {{ Carbon::parse($citaSeleccionada->hora_fin)->format('h:i A') }}@endif</div>
                    <div class="vc-detail-info-row"><i class="far fa-user"></i> {{ $citaSeleccionada->veterinario->name ?? 'Sin asignar' }}</div>
                    <div class="vc-detail-info-row"><i class="fas fa-users"></i> {{ $citaSeleccionada->mascota->cliente->nombreCompleto() }}</div>
                    @if ($citaSeleccionada->mascota->cliente->telefono)
                        <div class="vc-detail-info-row">
                            <i class="fab fa-whatsapp"></i>
                            <a href="https://wa.me/57{{ $citaSeleccionada->mascota->cliente->telefono }}" target="_blank">{{ $citaSeleccionada->mascota->cliente->telefono }}</a>
                        </div>
                    @endif

                    <div class="vc-detail-section-title">Motivo de la cita</div>
                    <div class="vc-detail-motivo">{{ $citaSeleccionada->motivo ?: 'Sin motivo registrado.' }}</div>

                    <div class="vc-detail-actions">
                        <button type="button" class="vc-btn vc-btn-success btn-block justify-content-center">
                            <i class="fas fa-pen"></i> Editar Cita
                        </button>
                        <div class="row-2">
                            <button type="button" class="vc-btn vc-btn-light justify-content-center">
                                <i class="far fa-calendar"></i> Reprogramar
                            </button>
                            <button type="button"
                                    class="vc-btn vc-btn-danger-outline justify-content-center btn-cancelar-cita"
                                    data-id="{{ $citaSeleccionada->id }}">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <button type="button" class="vc-btn vc-btn-outline justify-content-center">
                            <i class="fab fa-whatsapp" style="color:#25D366"></i> Enviar recordatorio por WhatsApp
                        </button>
                    </div>
                @else
                    <div class="vc-detail-empty">
                        <i class="far fa-calendar"></i>
                        Selecciona una cita del calendario para ver el detalle.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== MODAL NUEVA CITA ===================== --}}
    <div class="modal fade" id="modalNuevaCita" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <form id="formNuevaCita">
                    @csrf
                    <div class="modal-header" style="border-bottom: 1px solid var(--vc-border);">
                        <h5 class="modal-title" style="font-weight:700; font-size:15px;">
                            <i class="fas fa-calendar-plus" style="color: var(--vc-green)"></i> Nueva Cita
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="vc-field">
                            <label>Mascota</label>
                            <select name="mascota_id" class="form-control" required>
                                <option value="">Selecciona una mascota...</option>
                                @foreach ($mascotas as $mascota)
                                    <option value="{{ $mascota->id }}">
                                        {{ $mascota->nombre }} — {{ $mascota->cliente->nombreCompleto() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 vc-field">
                                <label>Veterinario</label>
                                <select name="veterinario_id" class="form-control">
                                    <option value="">Sin asignar</option>
                                    @foreach ($veterinarios as $vet)
                                        <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 vc-field">
                                <label>Sede</label>
                                <select name="sede_id" class="form-control">
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 vc-field">
                                <label>Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="{{ $fechaSeleccionada->toDateString() }}" required>
                            </div>
                            <div class="col-3 vc-field">
                                <label>Hora inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="col-3 vc-field">
                                <label>Hora fin</label>
                                <input type="time" name="hora_fin" class="form-control">
                            </div>
                        </div>

                        <div class="vc-field">
                            <label>Tipo de consulta</label>
                            <input type="text" name="tipo_consulta" class="form-control" placeholder="Consulta general, Vacunación, Control...">
                        </div>

                        <div class="vc-field">
                            <label>Motivo</label>
                            <textarea name="motivo" class="form-control" rows="2"></textarea>
                        </div>

                        <div id="erroresNuevaCita" class="text-danger small"></div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--vc-border);">
                        <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="vc-btn vc-btn-success">
                            <i class="fas fa-check"></i> Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-cancelar-cita').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('¿Cancelar esta cita? Esta acción no se puede deshacer.')) return;

                fetch(`/agenda/citas/${btn.dataset.id}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ estado: 'cancelada' }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                });
            });
        });
    });

    // Creación de citas vía AJAX (jQuery), consistente con el resto del sistema
    $(function () {
        $('#formNuevaCita').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true);
            $('#erroresNuevaCita').empty();

            $.ajax({
                url: '{{ route('agenda.citas.store') }}',
                method: 'POST',
                data: $form.serialize(),
                success: function (res) {
                    if (res.success) {
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false);
                    if (xhr.status === 422) {
                        const errores = xhr.responseJSON.errors;
                        const lista = Object.values(errores).flat().join('<br>');
                        $('#erroresNuevaCita').html(lista);
                    }
                }
            });
        });
    });
</script>
@endpush
