@extends('layouts.app')

@section('titulo', 'Agenda de Citas')

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
        <button type="button" class="vc-btn vc-btn-success" id="btnNuevaCita">
            <i class="fas fa-plus"></i> Nueva Cita
        </button>
        <button type="button" class="vc-btn vc-btn-outline">
            <i class="far fa-bell"></i> Recordatorios
        </button>
    </div>
</div>

{{-- ===================== FILTROS ===================== --}}
<form id="formFiltrosAgenda" class="vc-filters">
    <div class="vc-filter-group">
        <label>Veterinario</label>
        <select name="veterinario_id" id="filtroVeterinario">
            <option value="">Todos</option>
            @foreach ($veterinarios as $vet)
                <option value="{{ $vet->id }}">{{ $vet->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="vc-filter-group">
        <label>Sede</label>
        <select name="sede_id" id="filtroSede">
            <option value="">Principal</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div class="vc-filter-group">
        <label>Estado</label>
        <select name="estado" id="filtroEstado">
            <option value="">Todos</option>
            <option value="pendiente">Pendiente</option>
            <option value="confirmada">Confirmada</option>
            <option value="en_atencion">En atención</option>
            <option value="finalizada">Finalizada</option>
            <option value="cancelada">Cancelada</option>
        </select>        
    </div>
</form>

{{-- ===================== LAYOUT: CALENDARIO + DETALLE ===================== --}}
<div class="vc-agenda-layout">

    {{-- ============================================================ --}}
    {{-- COLUMNA IZQUIERDA: mini calendario + estados + resumen      --}}
    {{-- ============================================================ --}}
    <div id="columnaIzquierda">
        {{-- Se rellena por AJAX --}}
    </div>

    {{-- ============================================================ --}}
    {{-- COLUMNA CENTRAL: FullCalendar                                --}}
    {{-- ============================================================ --}}
    <div class="vc-agenda-center">
        <div class="vc-card" style="height: 100%;">
            <div class="vc-card__body" style="padding: 12px;">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- COLUMNA DERECHA: Detalle de la Cita                          --}}
    {{-- ============================================================ --}}
    <div class="vc-card vc-detail-panel" id="panelDetalle">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="far fa-calendar"></i> Detalle de la Cita</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-detail-empty">
                <i class="far fa-calendar"></i>
                Selecciona una cita del calendario para ver el detalle.
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL NUEVA / EDITAR CITA ===================== --}}
<div class="modal fade" id="modalNuevaCita" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <form id="formNuevaCita">
                @csrf
                <input type="hidden" name="id" id="citaId">
               
                <div class="modal-header" style="border-bottom:1px solid var(--vc-border);">
                    <h5 class="modal-title" id="tituloModalCita" style="font-weight:700;font-size:15px;">
                        <i class="fas fa-calendar-plus" style="color:var(--vc-green)"></i> Nueva Cita
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="vc-field">
                        <label for="citaMascota">Mascota <span class="text-danger">*</span></label>
                        <select name="mascota_id" id="citaMascota" class="form-control select2-mascota" required
                                style="width: 100%;">
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
                            <select name="veterinario_id" id="citaVeterinario" class="form-control">
                                <option value="">Sin asignar</option>
                                @foreach ($veterinarios as $vet)
                                    <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 vc-field">
                            <label>Sede</label>
                            <select name="sede_id" id="citaSede" class="form-control">
                                <option value="">Sin sede</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 vc-field">
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="citaFecha" class="form-control" required>
                        </div>
                        <div class="col-3 vc-field">
                            <label>Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" name="hora_inicio" id="citaHoraInicio" class="form-control" required>
                        </div>
                        <div class="col-3 vc-field">
                            <label>Hora fin</label>
                            <input type="time" name="hora_fin" id="citaHoraFin" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 vc-field">
                            <label>Tipo de consulta</label>
                            <select name="tipo_consulta" id="citaTipo" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="consulta_general">Consulta general</option>
                                <option value="vacunacion">Vacunación</option>
                                <option value="control">Control</option>
                                <option value="cirugia">Cirugía</option>
                                <option value="emergencia">Emergencia</option>
                                <option value="desparasitacion">Desparasitación</option>
                                <option value="estetica">Estética</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-6 vc-field">
                            <label>Estado</label>
                            <select name="estado" id="citaEstado" class="form-control">
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="en_atencion">En atención</option>
                                <option value="finalizada">Finalizada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="vc-field">
                        <label>Motivo</label>
                        <textarea name="motivo" id="citaMotivo" class="form-control" rows="2"></textarea>
                    </div>

                    <div id="erroresNuevaCita" class="text-danger small"></div>
                </div>

                <div class="modal-footer" style="border-top:1px solid var(--vc-border);">
                    <button type="button" class="vc-btn vc-btn-danger-outline" id="btnEliminarCita" style="display:none;">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <div style="margin-left:auto;">
                        <button type="button" class="vc-btn vc-btn-default mr-2" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="vc-btn vc-btn-success">
                            <i class="fas fa-check"></i> Guardar Cita
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    
    /* ============================================================ */
/* 🔥 LAYOUT 3 COLUMNAS: izq | calendario | detalle             */
/* ============================================================ */
.vc-agenda-layout {
    display: grid;
    grid-template-columns: 260px minmax(0, 1fr) 340px;
    gap: 18px;
    align-items: start;
    margin-top: 16px;
}

/* Columna central: calendar */
.vc-agenda-center {
    min-width: 0;
    overflow: hidden;
}

.vc-agenda-center .vc-card {
    overflow: hidden;
    height: 100%;
}

.vc-agenda-center .vc-card__body {
    overflow: hidden;
}

/* Columna izquierda: fija su ancho */
#columnaIzquierda {
    min-width: 0;
}

/* Columna derecha: panel de detalle */
.vc-detail-panel {
    min-width: 0;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    position: sticky;
    top: 20px;
}

/* ============================================================ */
/* 🔥 RESPONSIVE                                                */
/* ============================================================ */
@media (max-width: 1400px) {
    .vc-agenda-layout {
        grid-template-columns: 240px minmax(0, 1fr) 300px;
    }
}

@media (max-width: 1200px) {
    .vc-agenda-layout {
        grid-template-columns: 220px minmax(0, 1fr);
    }
    .vc-detail-panel {
        grid-column: 1 / -1;
        position: static;
        max-height: none;
    }
}

@media (max-width: 900px) {
    .vc-agenda-layout {
        grid-template-columns: 1fr;
    }
    #columnaIzquierda {
        order: 2;
    }
    .vc-agenda-center {
        order: 1;
    }
    .vc-detail-panel {
        order: 3;
    }
}

/* ===== Header de FullCalendar ===== */
.fc .fc-header-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px 12px;
    margin-bottom: 14px;
}

.fc .fc-toolbar-title {
    font-size: 1.15rem;
    font-weight: 700;
    text-transform: capitalize;
    color: #0F172A;
}

/* Cada grupo de botones alineado y con espacio entre botones */
.fc .fc-toolbar-chunk {
    display: flex;
    align-items: center;
    gap: 8px;
}

.fc .fc-button-group {
    display: flex;
    gap: 6px;
}

.fc .fc-button-group > .fc-button,
.fc .fc-header-toolbar .fc-button {
    margin: 0 !important;
    border-radius: 8px !important;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.2;
    box-shadow: none !important;
}

/* ============================================================ */
/* 🔥 FULLCALENDAR — ESTILOS                                    */
/* ============================================================ */
    #calendar {
        width: 100%;
        min-width: 0;
        background: #fff;
    }

    .fc {
        width: 100% !important;
        max-width: 100%;
    }

    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px !important;
    }

    .fc .fc-toolbar-title {
        font-size: 1.05rem !important;
        font-weight: 700;
        color: #1A2332;
        margin: 0 !important;
    }

    .fc .fc-button {
        text-transform: capitalize;
        font-weight: 600;
        font-size: .8rem;
        padding: 5px 10px;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .fc .fc-button-primary {
        background: #2F6FED;
        border-color: #2F6FED;
    }

    .fc .fc-button-primary:hover,
    .fc .fc-button-primary:focus {
        background: #2558c7;
        border-color: #2558c7;
    }

    .fc .fc-button-primary:not(:disabled):not(.disabled):active,
    .fc .fc-button-primary:not(:disabled):not(.disabled).active {
        background: #1e47a8;
        border-color: #1e47a8;
    }

    .fc .fc-button-primary:disabled {
        background: #94a3b8;
        border-color: #94a3b8;
    }

    .fc .fc-col-header-cell-cushion {
        padding: 8px 2px;
        font-weight: 700;
        color: #475569;
        text-transform: capitalize;
        font-size: .78rem;
    }

    .fc .fc-daygrid-day-number {
        font-weight: 600;
        color: #1A2332;
        padding: 4px 6px;
        font-size: .82rem;
    }

    .fc .fc-day-today {
        background: rgba(47, 111, 237, .07) !important;
    }

    .fc .fc-day-today .fc-daygrid-day-number {
        background: #2F6FED;
        color: #fff;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
        font-weight: 700;
    }

    .fc .fc-event {
        border: none;
        border-radius: 5px;
        padding: 2px 5px;
        font-size: .74rem;
        font-weight: 600;
        cursor: grab;
        overflow: hidden;
    }

    .fc .fc-event:hover {
        filter: brightness(1.08);
    }

    .fc .fc-event:active {
        cursor: grabbing;
    }

    .fc .fc-daygrid-more-link {
        font-weight: 700;
        color: #2F6FED;
        font-size: .75rem;
    }

    .fc .fc-popover {
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,.15);
        border: none;
    }

    .fc .fc-popover-header {
        background: #F8FAFC;
        border-radius: 10px 10px 0 0;
        font-weight: 700;
        color: #1A2332;
        padding: 8px 12px;
    }

    .fc .fc-timegrid-slot-label {
        font-size: .74rem;
        color: #64748B;
        font-weight: 600;
    }

    .fc .fc-timegrid-now-indicator-line {
        border-color: #dc3545;
        border-width: 2px;
    }

    .fc .fc-timegrid-now-indicator-arrow {
        border-color: #dc3545;
    }

    .vc-ev-line { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 1px 4px; font-size: 12px; }
    .fc-daygrid-event { border-radius: 3px; }
    .fc-popover { z-index: 1060; box-shadow: 0 8px 24px rgba(0,0,0,.18); }

        /* ===== Citas en vistas Semana / Día ===== */
    .fc-timegrid-slot {
        height: 3.2em;               /* más alto: una cita de 30 min ocupa ~50px */
    }

    .fc-timegrid-event {
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(0,0,0,.15);
    }

    .fc-timegrid-event .fc-event-main {
        padding: 0;
        overflow: hidden;
    }

    .vc-ev-card {
        padding: 3px 6px;
        line-height: 1.25;
        font-size: 11.5px;
        color: #fff;
        overflow: hidden;
    }

    .vc-ev-card > div {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .vc-ev-hora     { font-weight: 700; font-size: 11px; opacity: .95; }
    .vc-ev-paciente { font-weight: 600; }
    .vc-ev-tipo     { font-size: 10.5px; opacity: .9; }

        /* ===== Panel de detalle: altura según el contenido, sin scroll ===== */
    .vc-agenda-layout {
        align-items: start;          /* cada columna toma su propia altura */
    }

    #panelDetalle {
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        align-self: start;
    }

    #panelDetalle .vc-card__body {
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        padding: 14px 16px;
    }

    /* Filas más compactas y valores largos que bajan de línea en vez de desbordar */
    #panelDetalle .vc-resumen-row {
        align-items: flex-start;
        gap: 10px;
        padding: 6px 0;
    }

    #panelDetalle .vc-resumen-row strong {
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }
    
</style>
@endpush

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
@endpush

@push('js')
{{-- FullCalendar 6 --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/es.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Select2: quita esta línea si tu layout ya lo carga --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function () {
    'use strict';

    // ============================================================
    // CONFIG (las rutas se resuelven en Blade, deja este archivo dentro de la vista)
    // ============================================================
    const cfg = {
        rutaEventos: "{{ route('agenda.eventos') }}",
        rutaDatos:   "{{ route('agenda.datos') }}",
        rutaStore:   "{{ route('agenda.citas.store') }}",
        rutaBase:    "{{ url('agenda/citas') }}",
        csrfToken:   "{{ csrf_token() }}",
    };

    const estadoActual = {
        fecha: null,
        filtros: {
            veterinario_id: '',
            sede_id: '',
            estado: '',
        },
        citaSeleccionada: null,
    };

    let calendar = null;
    let citaActual = null;   // cita mostrada en el panel derecho
    let cargaId = 0;         // evita que una respuesta lenta pise a una más nueva

    const $  = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    // ============================================================
    // HELPERS
    // ============================================================
    const esc = (v) => String(v ?? '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');

    const pad = (n) => String(n).padStart(2, '0');

    const fechaISO = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const horaHM   = (d) => `${pad(d.getHours())}:${pad(d.getMinutes())}`;

    const headersJson = () => ({
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': cfg.csrfToken,
    });
    

    // ============================================================
    // FULLCALENDAR
    // ============================================================
    function inicializarCalendario() {
        const el = document.getElementById('calendar');
        if (!el) return;

        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar no está cargado');
            return;
        }

        calendar = new FullCalendar.Calendar(el, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay',
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week:  'Semana',
                day:   'Día',
            },
            firstDay: 1,
            height: 'auto',
            navLinks: true,
            selectable: true,
            editable: true,
            eventStartEditable: true,
            eventDurationEditable: true,
            nowIndicator: true,
            weekNumbers: false,
            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            slotDuration: '00:30:00',
            slotEventOverlap: false,
            eventMinHeight: 44,
            allDayText: 'Todo el día',
            noEventsText: 'No hay citas para mostrar',
            slotLabelFormat:  { hour: 'numeric', minute: '2-digit', hour12: true },
            eventTimeFormat:  { hour: 'numeric', minute: '2-digit', hour12: true },

            // Varias citas en un mismo día: se ven 2 y el resto va a "+N más" (popover)
            dayMaxEvents: 2,
            moreLinkClick: 'popover',
            moreLinkText: (n) => `+${n} más`,
            eventDisplay: 'block',
            displayEventTime: true,

            // Contenido de las citas según la vista
            eventContent: function (arg) {
                const props = arg.event.extendedProps || {};
                const tipoVista = arg.view.type;

                const fmt = (d) => d
                    ? d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }).toLowerCase()
                    : '';

                const tipoTxt = String(props.tipo_consulta || '')
                    .replace(/_/g, ' ')
                    .replace(/^./, (c) => c.toUpperCase());

                // Vista Mes: "7:30 am  Luna"
                if (tipoVista === 'dayGridMonth') {
                    const wrap = document.createElement('div');
                    wrap.className = 'vc-ev-line';

                    const b = document.createElement('b');
                    b.textContent = fmt(arg.event.start) + ' ';

                    wrap.append(b, document.createTextNode(arg.event.title));
                    return { domNodes: [wrap] };
                }

                // Semana / Día: hora, paciente - cliente, tipo de consulta
                if (tipoVista.startsWith('timeGrid')) {
                    const wrap = document.createElement('div');
                    wrap.className = 'vc-ev-card';

                    const rango = arg.event.end
                        ? `${fmt(arg.event.start)} – ${fmt(arg.event.end)}`
                        : fmt(arg.event.start);

                    const l1 = document.createElement('div');
                    l1.className = 'vc-ev-hora';
                    l1.textContent = rango;

                    const l2 = document.createElement('div');
                    l2.className = 'vc-ev-paciente';
                    const nombre = props.mascota || arg.event.title;
                    l2.textContent = props.cliente && props.cliente !== '—'
                        ? `${nombre} - ${props.cliente}`
                        : nombre;

                    wrap.append(l1, l2);

                    if (tipoTxt) {
                        const l3 = document.createElement('div');
                        l3.className = 'vc-ev-tipo';
                        l3.textContent = tipoTxt;
                        wrap.append(l3);
                    }

                    return { domNodes: [wrap] };
                }
                // Otras vistas: render por defecto
            },

            // Tooltip con el detalle completo
            eventDidMount: function (info) {
                const p = info.event.extendedProps || {};
                const tipo = String(p.tipo_consulta || '').replace(/_/g, ' ');
                info.el.title = [
                    `${p.hora_inicio_fmt || ''}${p.hora_fin_fmt ? ' – ' + p.hora_fin_fmt : ''}`,
                    `${p.mascota || info.event.title} - ${p.cliente || ''}`,
                    tipo,
                ].filter(Boolean).join('\n');
            },

            // Eventos por AJAX
            events: function (info, successCallback, failureCallback) {
                const params = new URLSearchParams({
                    start: info.startStr,
                    end:   info.endStr,
                });

                if (estadoActual.filtros.veterinario_id) params.set('veterinario_id', estadoActual.filtros.veterinario_id);
                if (estadoActual.filtros.sede_id)        params.set('sede_id', estadoActual.filtros.sede_id);
                if (estadoActual.filtros.estado)         params.set('estado', estadoActual.filtros.estado);

                fetch(`${cfg.rutaEventos}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(async (res) => {
                    const txt = await res.text();
                    if (!res.ok) {
                        console.error(`❌ eventos respondió ${res.status}:`, txt.slice(0, 600));
                        throw new Error(`eventos ${res.status}`);
                    }
                    try {
                        return JSON.parse(txt);
                    } catch (e) {
                        console.error('❌ eventos no devolvió JSON limpio:', txt.slice(0, 600));
                        throw e;
                    }
                })
                .then((data) => successCallback(data))
                .catch((err) => failureCallback(err));
            },

            // 🔥 SINCRONIZACIÓN: el calendario principal avisa al mini calendario
            datesSet: function (info) {
                const ini = info.view.currentStart;
                const fin = info.view.currentEnd;

                const sel = estadoActual.fecha ? new Date(estadoActual.fecha + 'T00:00:00') : null;
                if (sel && sel >= ini && sel < fin) return; // ya está sincronizado

                // Si hoy está en el rango se selecciona hoy; si no, el primer día del rango
                const hoy = new Date();
                const ref = (hoy >= ini && hoy < fin) ? hoy : ini;

                estadoActual.fecha = fechaISO(ref);
                estadoActual.citaSeleccionada = null;
                cargarDatos({ desdeCalendario: true });
            },

            // Click en cita → panel de detalle
            eventClick: function (info) {
                info.jsEvent.preventDefault();

                const props = info.event.extendedProps || {};
                estadoActual.citaSeleccionada = info.event.id;

                renderPanelDetalle({
                    id:               info.event.id,
                    estado:           props.estado || 'pendiente',
                    mascota:          props.mascota || info.event.title,
                    mascota_raza:     props.mascota_raza,
                    mascota_especie:  props.mascota_especie,
                    cliente:          props.cliente,
                    cliente_telefono: props.cliente_celular || props.cliente_tel,
                    veterinario:      props.veterinario,
                    veterinario_id:   props.veterinario_id,
                    sede_id:          props.sede_id,
                    fecha:            info.event.startStr.substring(0, 10),
                    fecha_titulo:     new Date(info.event.start).toLocaleDateString('es-CO', {
                        weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
                    }),
                    hora_inicio:      info.event.startStr.substring(11, 16),
                    hora_fin:         info.event.endStr ? info.event.endStr.substring(11, 16) : '',
                    hora_inicio_fmt:  props.hora_inicio_fmt,
                    hora_fin_fmt:     props.hora_fin_fmt,
                    tipo_consulta:    props.tipo_consulta,
                    motivo:           props.motivo,
                    mascota_id:       props.mascota_id,
                });
            },

            // Click en día / espacio, o seleccionar rango → nueva cita
            // (un clic simple también dispara select; con dateClick el modal se abría dos veces)
            select: function (info) {
                abrirModalNueva(info.startStr);
                calendar.unselect();
            },

            // Arrastrar / redimensionar → actualizar por AJAX
            eventDrop:   function (info) { actualizarFechaCita(info); },
            eventResize: function (info) { actualizarFechaCita(info); },
        });

        // render() dispara datesSet, que a su vez llama a cargarDatos() una sola vez
        calendar.render();
    }

    // ============================================================
    // CARGAR DATOS (mini calendario + estados + resumen + detalle)
    // ============================================================
    async function cargarDatos({ desdeCalendario = false } = {}) {
        const miCarga = ++cargaId;
        const params = new URLSearchParams();

        if (estadoActual.fecha)                  params.set('fecha', estadoActual.fecha);
        if (estadoActual.filtros.veterinario_id) params.set('veterinario_id', estadoActual.filtros.veterinario_id);
        if (estadoActual.filtros.sede_id)        params.set('sede_id', estadoActual.filtros.sede_id);
        if (estadoActual.filtros.estado)         params.set('estado', estadoActual.filtros.estado);
        if (estadoActual.citaSeleccionada)       params.set('cita_id', estadoActual.citaSeleccionada);

        try {
            const res = await fetch(`${cfg.rutaDatos}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!res.ok) throw new Error('Error al cargar los datos');

            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Error');

            if (miCarga !== cargaId) return; // llegó una petición más nueva

            estadoActual.fecha = data.fechaSeleccionada;

            const safe = (fn, ...args) => {
                try { fn(...args); }
                catch (e) { console.error(`❌ Error en ${fn.name}:`, e); }
            };

            safe(renderColumnaIzquierda, data);
            safe(renderPanelDetalle, data.citaSeleccionada);

            // Solo mover el calendario si el cambio NO vino de él
            if (calendar && !desdeCalendario) {
                calendar.gotoDate(data.fechaSeleccionada);
            }
        } catch (err) {
            console.error('❌ Error al cargar datos:', err);
        }
    }

    // ============================================================
    // COLUMNA IZQUIERDA: mini calendario + estados (mes) + resumen del día
    // ============================================================
    function renderColumnaIzquierda(data) {
        const cont = document.getElementById('columnaIzquierda');
        if (!cont) return;

        const mapaColores = {
            confirmada:  'green',
            pendiente:   'orange',
            en_atencion: 'blue',
            finalizada:  'purple',
            cancelada:   'red',
        };

        // ---------- Mini calendario ----------
        let calHTML = `
            <div class="vc-card mb-3">
                <div class="vc-card__body">
                    <div class="vc-cal-nav">
                        <button type="button" class="vc-cal-prev"><i class="fas fa-chevron-left"></i></button>
                        <strong style="font-size:13px">${esc(data.calendario?.nombreMes || '')}</strong>
                        <button type="button" class="vc-cal-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="vc-cal-grid">
                        ${['Lu','Ma','Mi','Ju','Vi','Sá','Do'].map(d => `<div class="vc-cal-dow">${d}</div>`).join('')}
        `;

        Object.values(data.calendario?.semanas || {}).forEach(semana => {
            Object.values(semana || {}).forEach(dia => {
                const primerEstado = Object.values(dia.estados || {})[0] || null;
                const dotColor = primerEstado ? (mapaColores[primerEstado] || 'blue') : null;

                calHTML += `
                    <a href="#" data-fecha="${dia.fecha}"
                       class="vc-cal-day ${!dia.esDelMes ? 'is-muted' : ''} ${dia.esSeleccionado ? 'is-today' : ''}">
                        ${dia.dia}
                        ${dotColor ? `<span class="dot dot--${dotColor}"></span>` : ''}
                    </a>
                `;
            });
        });

        calHTML += `
                    </div>
                </div>
            </div>
        `;

        // ---------- Helpers de filas ----------
        const filaStyle = 'display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 0;border-bottom:1px solid #EEF1F6;';
        const numStyle  = 'min-width:28px;text-align:center;padding:1px 8px;border-radius:999px;background:#F1F5F9;color:#0F172A;font-weight:700;font-size:13px;';

        const coloresHex = {
            green:  '#16A34A',
            orange: '#F59E0B',
            blue:   '#2F6FED',
            purple: '#8B5CF6',
            red:    '#EF4444',
        };

        // ---------- Estados (conteo del MES) ----------
        const estados = [
            { key: 'confirmada',  label: 'Confirmada',  color: 'green'  },
            { key: 'pendiente',   label: 'Pendiente',   color: 'orange' },
            { key: 'en_atencion', label: 'En atención', color: 'blue'   },
            { key: 'finalizada',  label: 'Finalizada',  color: 'purple' },
            { key: 'cancelada',   label: 'Cancelada',   color: 'red'    },
        ];

        let estadosHTML = `
            <div class="vc-card mb-3">
                <div class="vc-card__header">
                    <span class="vc-card__title">Estados de citas · ${esc(data.calendario?.nombreMes || '')}</span>
                </div>
                <div class="vc-card__body">
        `;

        estados.forEach(e => {
            const total = Number(data.conteoEstados?.[e.key] ?? 0);
            estadosHTML += `
                <div class="vc-estado-row" style="${filaStyle}">
                    <span style="display:flex;align-items:center;gap:8px;font-size:13px;">
                        <i style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${coloresHex[e.color]};"></i>
                        ${e.label}
                    </span>
                    <strong style="${numStyle}">${total}</strong>
                </div>
            `;
        });

        estadosHTML += `</div></div>`;

        // ---------- Resumen del día ----------
        const r = data.resumenDia || {};
        const filasResumen = [
            { icono: 'far fa-calendar',  label: 'Total citas', valor: r.total       ?? 0 },
            { icono: 'far fa-clock',     label: 'En atención', valor: r.en_atencion ?? 0 },
            { icono: 'fas fa-check',     label: 'Finalizadas', valor: r.finalizadas ?? 0 },
            { icono: 'far fa-hourglass', label: 'Pendientes',  valor: r.pendientes  ?? 0 },
        ];

        const resumenHTML = `
            <div class="vc-card">
                <div class="vc-card__header">
                    <span class="vc-card__title"><i class="fas fa-paw"></i> Resumen del día</span>
                </div>
                <div class="vc-card__body">
                    ${filasResumen.map(f => `
                        <div class="vc-resumen-row" style="${filaStyle}">
                            <span style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <i class="${f.icono}" style="width:16px;text-align:center;color:#64748B;"></i>
                                ${f.label}
                            </span>
                            <strong style="${numStyle}">${Number(f.valor)}</strong>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        cont.innerHTML = calHTML + estadosHTML + resumenHTML;

        // ---------- Navegación del mini calendario ----------
        const moverMes = (delta) => {
            const [y, m] = estadoActual.fecha.split('-').map(Number);
            const d = new Date(y, m - 1 + delta, 1);
            estadoActual.fecha = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-01`;
            estadoActual.citaSeleccionada = null;
            cargarDatos(); // ya mueve el calendario principal
        };

        cont.querySelector('.vc-cal-prev')?.addEventListener('click', () => moverMes(-1));
        cont.querySelector('.vc-cal-next')?.addEventListener('click', () => moverMes(1));

        cont.querySelectorAll('.vc-cal-day').forEach(a => {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                estadoActual.fecha = a.dataset.fecha;
                estadoActual.citaSeleccionada = null;
                cargarDatos(); // ya mueve el calendario principal
            });
        });
    }

    // ============================================================
    // PANEL DERECHO: detalle de la cita
    // ============================================================
    function renderPanelDetalle(cita) {
        const body = document.querySelector('#panelDetalle .vc-card__body');
        if (!body) return;

        citaActual = cita || null;

        if (!cita) {
            body.innerHTML = `
                <div class="vc-detail-empty">
                    <i class="far fa-calendar"></i>
                    Selecciona una cita del calendario para ver el detalle.
                </div>`;
            return;
        }

        const estados = {
            pendiente:   { label: 'Pendiente',   color: '#F59E0B' },
            confirmada:  { label: 'Confirmada',  color: '#16A34A' },
            en_atencion: { label: 'En atención', color: '#2F6FED' },
            finalizada:  { label: 'Finalizada',  color: '#8B5CF6' },
            cancelada:   { label: 'Cancelada',   color: '#EF4444' },
        };
        const est = estados[cita.estado] || { label: cita.estado || '—', color: '#6B7280' };

        const fechaTitulo = cita.fecha_titulo || (cita.fecha
            ? new Date(cita.fecha + 'T00:00:00').toLocaleDateString('es-CO', {
                weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
              })
            : '—');

        const horaIni = cita.hora_inicio_fmt || (cita.hora_inicio || '').substring(0, 5);
        const horaFin = cita.hora_fin_fmt || (cita.hora_fin || '').substring(0, 5);
        const horario = horaIni ? (horaFin ? `${horaIni} – ${horaFin}` : horaIni) : '—';

        const telefono = cita.cliente_celular || cita.cliente_telefono || cita.cliente_tel
            || cita.celular || cita.telefono || '';
        const tipo = String(cita.tipo_consulta || 'consulta_general').replace(/_/g, ' ');
        const detalleMascota = [cita.mascota_especie, cita.mascota_raza || cita.raza]
            .filter(Boolean).join(' · ');

        const fila = (icono, label, valor) => `
            <div class="vc-resumen-row">
                <span class="label"><i class="${icono}"></i> ${label}</span>
                <strong>${valor}</strong>
            </div>`;

        body.innerHTML = `
            <div style="margin-bottom:12px;">
                <div style="font-size:16px;font-weight:700;">
                    <i class="fas fa-paw"></i> ${esc(cita.mascota || 'Cita')}
                </div>
                ${detalleMascota ? `<div class="text-muted small">${esc(detalleMascota)}</div>` : ''}
                <span style="display:inline-block;margin-top:6px;padding:2px 10px;border-radius:999px;
                             font-size:12px;font-weight:600;color:#fff;background:${est.color};">
                    ${esc(est.label)}
                </span>
            </div>
            ${fila('far fa-calendar', 'Fecha', esc(fechaTitulo))}
            ${fila('far fa-clock', 'Horario', esc(horario))}
            ${fila('far fa-user', 'Cliente', esc(cita.cliente || '—'))}
            ${fila('fas fa-phone', 'Celular', esc(telefono || '—'))}
            ${fila('fas fa-user-md', 'Veterinario', esc(cita.veterinario || 'Sin asignar'))}
            ${fila('fas fa-stethoscope', 'Tipo', esc(tipo))}
            <div style="margin-top:12px;">
                <div class="text-muted small">Motivo</div>
                <div>${esc(cita.motivo || 'Sin motivo registrado.')}</div>
            </div>
            <div style="margin-top:16px;">
                <button type="button" class="vc-btn vc-btn-primary" id="btnEditarCita"
                        style="background:#16A34A;border:1px solid #16A34A;color:#fff;">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </div>`;

        document.getElementById('btnEditarCita')?.addEventListener('click', () => abrirModalEditar(cita));
    }

    // ============================================================
    // MODAL: NUEVA / EDITAR / GUARDAR / ELIMINAR
    // ============================================================
    // Asigna la mascota y avisa a Select2 para que refresque lo que muestra
    function setMascota(id) {
        const el = $('#citaMascota');
        if (!el) return;
        el.value = id || '';
        if (window.jQuery) window.jQuery(el).trigger('change');
    }

    function resetModal() {
        $('#formNuevaCita')?.reset();
        setMascota('');
        $('#citaId').value = '';
        $('#erroresNuevaCita').innerHTML = '';
        $('#btnEliminarCita').style.display = 'none';
        $('#tituloModalCita').innerHTML =
            '<i class="fas fa-calendar-plus" style="color:var(--vc-green)"></i> Nueva Cita';
    }

    function abrirModalNueva(fechaStr) {
        resetModal();

        const [fecha, hora] = String(fechaStr || fechaISO(new Date())).split('T');
        $('#citaFecha').value = fecha;

        // Con hora (clic en un espacio de Semana/Día) se usa esa hora; si no, la hora actual.
        // En ambos casos la hora fin queda 30 minutos después de la de inicio.
        horasAutomaticas(hora ? hora.substring(0, 5) : null);

        window.jQuery('#modalNuevaCita').modal('show');
    }

    function abrirModalEditar(cita) {
        resetModal();

        $('#citaId').value          = cita.id || '';
        setMascota(cita.mascota_id);
        $('#citaVeterinario').value = cita.veterinario_id || '';
        $('#citaSede').value        = cita.sede_id || '';
        $('#citaFecha').value       = cita.fecha || '';
        $('#citaHoraInicio').value  = (cita.hora_inicio || '').substring(0, 5);
        $('#citaHoraFin').value     = (cita.hora_fin || '').substring(0, 5);
        $('#citaTipo').value        = cita.tipo_consulta || '';
        $('#citaEstado').value      = cita.estado || 'pendiente';
        $('#citaMotivo').value      = cita.motivo || '';

        $('#btnEliminarCita').style.display = '';
        $('#tituloModalCita').innerHTML =
            '<i class="fas fa-edit" style="color:var(--vc-green)"></i> Editar Cita';

        window.jQuery('#modalNuevaCita').modal('show');
    }

    function mostrarErrores(json) {
        const box = $('#erroresNuevaCita');
        if (json && json.errors) {
            box.innerHTML = Object.values(json.errors).flat().map(m => `<div>${esc(m)}</div>`).join('');
        } else {
            box.textContent = (json && json.message) || 'No se pudo guardar la cita.';
        }
    }

    // ============================================================
    // AVISOS (SweetAlert2)
    // ============================================================
    function avisoCitaGuardada() {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 no está cargado; se usa alert() como respaldo.');
            alert('Cita guardada');
            return;
        }
        Swal.fire({
            icon: 'success',
            title: 'Cita guardada',
            timer: 1800,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }

    function avisoCitaMovida() {
        const msg = 'Cita cambiada correctamente';

        if (typeof toastr !== 'undefined') {
            toastr.success(msg);
            return;
        }

        console.warn('toastr no está cargado; se usa un aviso alternativo.');
        if (typeof Swal !== 'undefined') {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg,
                        showConfirmButton: false, timer: 2500, timerProgressBar: true });
        }
    }

    let guardando = false;

    async function guardarCita(e) {
        e.preventDefault();
        if (guardando) return;          // evita envíos repetidos
        guardando = true;

        const btn = $('#formNuevaCita button[type="submit"]');
        if (btn) btn.disabled = true;

        $('#erroresNuevaCita').innerHTML = '';

        const form = $('#formNuevaCita');
        const id = $('#citaId').value;
        const fd = new FormData(form);
        if (id) fd.append('_method', 'PUT');

        try {
            const res = await fetch(id ? `${cfg.rutaBase}/${id}` : cfg.rutaStore, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': cfg.csrfToken,
                },
                body: fd,
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok) {
                console.warn(`⚠️ store respondió ${res.status}:`, json);
                mostrarErrores(json);
                return;
            }

            window.jQuery('#modalNuevaCita').modal('hide');
            avisoCitaGuardada();
            if (calendar) calendar.refetchEvents();

            // Si se guardó en otro día, mover ambos calendarios a esa fecha
            estadoActual.fecha = $('#citaFecha').value || estadoActual.fecha;
            estadoActual.citaSeleccionada = (json.cita && json.cita.id) || json.id || id || null;
            cargarDatos();
        } catch (err) {
            console.error('❌ Error al guardar la cita:', err);
            mostrarErrores({ message: 'Error de conexión al guardar la cita.' });
        } finally {
            guardando = false;
            if (btn) btn.disabled = false;
        }
    }

    function confirmarEliminar() {
        if (typeof Swal === 'undefined') {
            console.warn('SweetAlert2 no está cargado; se usa confirm() como respaldo.');
            return Promise.resolve(confirm('¿Eliminar esta cita? Esta acción no se puede deshacer.'));
        }

        return Swal.fire({
            title: '¿Eliminar esta cita?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            reverseButtons: true,
            focusCancel: true,
        }).then((r) => r.isConfirmed);
    }

    async function eliminarCita() {
        const id = $('#citaId').value;
        if (!id) return;
        if (!(await confirmarEliminar())) return;

        try {
            const res = await fetch(`${cfg.rutaBase}/${id}`, {
                method: 'DELETE',
                headers: headersJson(),
            });
            if (!res.ok) throw new Error('No se pudo eliminar');

            window.jQuery('#modalNuevaCita').modal('hide');
            estadoActual.citaSeleccionada = null;
            if (calendar) calendar.refetchEvents();
            cargarDatos({ desdeCalendario: true });
        } catch (err) {
            console.error('❌ Error al eliminar la cita:', err);
            mostrarErrores({ message: 'No se pudo eliminar la cita.' });
        }
    }

    // ============================================================
    // ARRASTRAR / REDIMENSIONAR
    // ============================================================
    async function actualizarFechaCita(info) {
        const ev = info.event;

        // Una cita no puede quedar en la fila "Todo el día" (no tiene hora)
        if (ev.allDay || !ev.start) { info.revert(); return; }

        const fin = ev.end || new Date(ev.start.getTime() + 30 * 60000);
        // Si el fin cae en otro día, se deja sin hora de fin
        const mismoDia = fechaISO(fin) === fechaISO(ev.start);

        try {
            const res = await fetch(`${cfg.rutaBase}/${ev.id}/mover`, {
                method: 'PATCH',
                headers: headersJson(),
                body: JSON.stringify({
                    fecha:       fechaISO(ev.start),
                    hora_inicio: horaHM(ev.start),
                    hora_fin:    mismoDia ? horaHM(fin) : null,
                }),
            });

            const txt = await res.text();
            let json = {};
            try { json = JSON.parse(txt); } catch (_) {}

            if (!res.ok) {
                console.error(`❌ mover respondió ${res.status}:`, txt.slice(0, 600));
                info.revert();

                const msg = (json.errors && Object.values(json.errors).flat()[0])
                    || json.message
                    || 'No se pudo mover la cita.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'No se pudo mover la cita', text: msg });
                } else {
                    alert(msg);
                }
                return;
            }

            avisoCitaMovida();

            // Refresca eventos (hora formateada, tooltip) y columnas laterales
            if (calendar) calendar.refetchEvents();
            cargarDatos({ desdeCalendario: true });
        } catch (err) {
            console.error('❌ Error al mover la cita:', err);
            info.revert();
        }
    }

    // ============================================================
    // ARRANQUE
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        inicializarCalendario();

        $('#btnNuevaCita')?.addEventListener('click', () => abrirModalNueva());
        $('#formNuevaCita')?.addEventListener('submit', guardarCita);
        $('#btnEliminarCita')?.addEventListener('click', eliminarCita);

        // Filtros → actualizar estado, recargar eventos y columna izquierda
        const mapaFiltros = {
            '#filtroVeterinario': 'veterinario_id',
            '#filtroSede':        'sede_id',
            '#filtroEstado':      'estado',
        };
        Object.entries(mapaFiltros).forEach(([sel, key]) => {
            $(sel)?.addEventListener('change', (e) => {
                estadoActual.filtros[key] = e.target.value;
                if (calendar) calendar.refetchEvents();
                cargarDatos({ desdeCalendario: true });
            });
        });

        // Select2 con búsqueda para las mascotas (busca por mascota o por propietario)
        if (window.jQuery && window.jQuery.fn.select2 && $('#citaMascota')) {
            window.jQuery('#citaMascota').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: window.jQuery('#modalNuevaCita'),   // necesario dentro de un modal
                placeholder: 'Busca una mascota o propietario...',
                allowClear: true,
                minimumInputLength: 1,   // la lista no se despliega al hacer clic: solo muestra coincidencias al escribir
                language: {
                    inputTooShort: () => 'Escribe el nombre de la mascota o del propietario…',
                    noResults: () => 'Sin resultados',
                    searching: () => 'Buscando…',
                },
            });

            // Al abrir el Select2, el cursor queda listo en el buscador (Select2 4.1 ya no lo hace solo)
            window.jQuery('#citaMascota').on('select2:open', function () {
                setTimeout(function () {
                    const buscador = document.querySelector('.select2-container--open .select2-search__field');
                    if (buscador) buscador.focus();
                }, 0);
            });
        } else {
            console.warn('Select2 no está cargado; se usa el select normal.');
        }

        // Al cambiar la hora de inicio, la hora fin pasa a inicio + 30 min
        // (en una cita existente solo si la fin quedó vacía o antes del inicio)
        $('#citaHoraInicio')?.addEventListener('change', function () {
            if (!this.value) return;
            const fin = $('#citaHoraFin');
            const editando = !!$('#citaId').value;
            if (editando && fin.value && fin.value > this.value) return;

            const [h, m] = this.value.split(':').map(Number);
            const base = new Date();
            base.setHours(h, m, 0, 0);
            fin.value = horaMas(30, base);
        });

        // Limpiar errores al cerrar el modal
        window.jQuery('#modalNuevaCita').on('hidden.bs.modal', function () {
            $('#erroresNuevaCita').innerHTML = '';
        });
    });

    // ============================================================
    // HORAS AUTOMÁTICAS: inicio = ahora (o la hora pulsada), fin = inicio + 30 min
    // ============================================================
    function horaMas(minutos, base) {
        const d = new Date(base);
        d.setMinutes(d.getMinutes() + minutos);
        // Un campo time no distingue días: si pasa de medianoche se deja en 23:59
        if (d.getDate() !== base.getDate()) return '23:59';
        return horaHM(d);
    }

    function horasAutomaticas(horaInicio) {
        const inicio = $('#citaHoraInicio');
        const fin    = $('#citaHoraFin');
        if (!inicio || !fin) return;

        const base = new Date();
        if (horaInicio) {
            const [h, m] = horaInicio.split(':').map(Number);
            base.setHours(h, m, 0, 0);
        }

        inicio.value = horaHM(base);
        fin.value    = horaMas(30, base);
    }

})();

</script>
@endpush