@extends('layouts.app')

{{-- El layout arma el <title> como "{config('adminlte.title')} | {subtitle}" --}}
@section('title', 'Inicio')

@section('content_body')

<br>

<div class="vc-page-header">
    <div class="vc-page-header__left">
        <span class="vc-page-icon"><i class="far fa-calendar"></i></span>
        <div>
            <h1>Inicio</h1>
            <p>Tablero de control de información</p>
        </div>
    </div>
</div>

{{-- ===================== STAT CARDS ===================== --}}
<div class="vc-stats">

    <div class="vc-stat vc-stat--blue">
        <span class="vc-stat__icon"><i class="fas fa-users"></i></span>
        <div>
            <div class="vc-stat__label">Pacientes Hoy</div>
            <div class="vc-stat__value">{{ $pacientesHoy ?? 0 }}</div>
            <span class="vc-stat__sub">
                <span class="vc-stat__delta up"><i class="fas fa-arrow-up"></i> 12%</span> vs ayer
            </span>
        </div>
    </div>

    <div class="vc-stat vc-stat--green">
        <span class="vc-stat__icon"><i class="far fa-calendar-check"></i></span>
        <div>
            <div class="vc-stat__label">Citas Hoy</div>
            <div class="vc-stat__value">{{ $citasHoy ?? 0 }}</div>
            <span class="vc-stat__sub">
                <span class="vc-stat__delta up"><i class="fas fa-arrow-up"></i> 25%</span> vs ayer
            </span>
        </div>
    </div>

    <div class="vc-stat vc-stat--purple">
        <span class="vc-stat__icon"><i class="fas fa-shopping-cart"></i></span>
        <div>
            <div class="vc-stat__label">Ventas Hoy</div>
            <div class="vc-stat__value">${{ number_format($ventasHoy ?? 0, 0, ',', '.') }}</div>
            <span class="vc-stat__sub">
                <span class="vc-stat__delta up"><i class="fas fa-arrow-up"></i> 18%</span> vs ayer
            </span>
        </div>
    </div>

    <div class="vc-stat vc-stat--orange">
        <span class="vc-stat__icon"><i class="fas fa-box"></i></span>
        <div>
            <div class="vc-stat__label">Inventario Bajo</div>
            <div class="vc-stat__value">{{ $inventarioBajo ?? 0 }}</div>
            <span class="vc-stat__sub">
                <span class="vc-stat__delta down"><i class="fas fa-arrow-down"></i> 15%</span> vs ayer
            </span>
        </div>
    </div>

    <div class="vc-stat vc-stat--teal">
        <span class="vc-stat__icon"><i class="fas fa-paw"></i></span>
        <div>
            <div class="vc-stat__label">Mascotas Registradas</div>
            <div class="vc-stat__value">{{ number_format($mascotasRegistradas ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- ===================== FILA 2 ===================== --}}
<div class="vc-row-3">

    <!-- Calendario -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="far fa-calendar"></i> Calendario</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-cal-nav">
                <button type="button" id="calPrev"><i class="fas fa-chevron-left"></i></button>
                <strong id="calTitle"></strong>
                <button type="button" id="calNext"><i class="fas fa-chevron-right"></i></button>
            </div>

            <div id="fullcalendar"></div>
        </div>
    </div>

    <!-- Citas (sincronizado con el calendario) -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title">
                <i class="far fa-calendar"></i> <span id="citasTitulo">Citas de hoy</span>
            </span>
        </div>
        <div class="vc-card__body">
            <div class="vc-list" id="proximasCitasList">
                <p class="text-muted small mb-0">Cargando citas...</p>
            </div>
            <a href="{{ route('agenda') }}" class="vc-card__link">Ver todas las citas <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Pacientes Recientes -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="far fa-address-card"></i> Pacientes Recientes</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-list">
                @forelse ($pacientesRecientes ?? [] as $mascota)
                    <div class="vc-list-item">
                        <img class="vc-list-avatar"
                             src="https://ui-avatars.com/api/?name={{ urlencode($mascota->nombre) }}&background=E3F9F6&color=14B8A6"
                             alt="{{ $mascota->nombre }}">
                        <div class="vc-list-info">
                            <strong>{{ $mascota->nombre }}</strong>
                            <span>{{ $mascota->raza }}</span>
                        </div>
                        <span class="vc-list-date">{{ $mascota->created_at->format('d M Y') }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay pacientes recientes.</p>
                @endforelse
            </div>
            <a href="{{ route('mascotas') }}" class="vc-card__link">Ver todos los pacientes <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</div>

{{-- ===================== FILA 3 ===================== --}}
<div class="vc-row-3b">

    <!-- Ventas -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-chart-line"></i> Ventas</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-chart-wrap">
                <canvas id="chartVentas"></canvas>
            </div>
        </div>
    </div>

    <!-- Inventario Bajo -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-exclamation-triangle"></i> Inventario Bajo</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-list">
                @forelse ($inventarioBajoLista ?? [] as $producto)
                    <div class="vc-inv-item">
                        <span class="vc-inv-icon"><i class="fas fa-vial"></i></span>
                        <div class="vc-inv-info">
                            <strong>{{ $producto->nombre }}</strong>
                        </div>
                        <div class="text-right">
                            <span class="d-block small text-muted mb-1">Stock: {{ $producto->stock }}</span>
                            <span class="vc-badge-pill bajo">Bajo</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay productos con inventario bajo.</p>
                @endforelse
            </div>
            <a href="{{ route('inventario') }}" class="vc-card__link">Ver inventario completo <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-bolt"></i> Acciones Rápidas</span>
        </div>
        <div class="vc-card__body">
            <div class="vc-quick-grid">
                <a href="{{ route('agenda') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon blue"><i class="far fa-calendar"></i></span>
                    Nueva Cita
                </a>
                <a href="{{ route('clientes') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon teal"><i class="fas fa-user-plus"></i></span>
                    Registrar Cliente
                </a>
                <a href="{{ route('mascotas') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon blue"><i class="fas fa-paw"></i></span>
                    Registrar Mascota
                </a>
                <a href="{{ route('historias') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon teal"><i class="fas fa-stethoscope"></i></span>
                    Nueva Consulta
                </a>
                <a href="{{ route('ventas.pos') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon green"><i class="fas fa-shopping-cart"></i></span>
                    Venta / POS
                </a>
                <a href="{{ route('inventario') }}" class="vc-quick-btn">
                    <span class="vc-quick-icon orange"><i class="fas fa-box"></i></span>
                    Agregar Producto
                </a>
            </div>
        </div>
    </div>
</div>

<br>

@stop

@push('css')
<style>
    /* Lista de citas interactiva */
    #proximasCitasList .vc-list-item { cursor: pointer; transition: background .15s; border-radius: 10px; }
    #proximasCitasList .vc-list-item:hover { background: #F5F8FF; }
    #proximasCitasList .vc-list-item.is-active { background: #EEF4FF; box-shadow: inset 3px 0 0 #2F6FED; }

    /* Calendario simple */
    #fullcalendar { font-size: .85rem; }
    #fullcalendar .fc-daygrid-day { cursor: pointer; }
    #fullcalendar .fc-daygrid-day.fc-day-today { background: rgba(47,111,237,.08); }
    #fullcalendar .fc-highlight { background: rgba(47,111,237,.18); }
    #fullcalendar .fc-col-header-cell-cushion,
    #fullcalendar .fc-daygrid-day-number { color: #475569; text-decoration: none; }
    #fullcalendar .fc-day-other .fc-daygrid-day-number { color: #CBD5E1; }
</style>
@endpush

{{-- ===================== CALENDARIO + CITAS ===================== --}}
@push('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/es.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const hoy = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD en hora local
    let fechaSel = hoy;
    let citaSel = null;

    const listEl = document.getElementById('proximasCitasList');
    const tituloEl = document.getElementById('citasTitulo');

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    function renderLista() {
        const [y, m, d] = fechaSel.split('-').map(Number);
        const etiqueta = new Date(y, m - 1, d).toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long'
        });
        tituloEl.textContent = fechaSel === hoy ? 'Citas de hoy' : 'Citas · ' + etiqueta;

        const citas = calendar.getEvents()
            .filter(ev => ev.extendedProps.fecha === fechaSel)
            .sort((a, b) => a.start - b.start);

        if (!citas.length) {
            listEl.innerHTML = '<p class="text-muted small mb-0">No hay citas para este día.</p>';
            return;
        }

        listEl.innerHTML = citas.map(ev => {
            const p = ev.extendedProps;
            const estadoTxt = p.estado.replace('_', ' ');
            return `
            <div class="vc-list-item ${String(ev.id) === String(citaSel) ? 'is-active' : ''}" data-id="${esc(ev.id)}">
                <img class="vc-list-avatar"
                     src="https://ui-avatars.com/api/?name=${encodeURIComponent(p.mascota)}&background=E8F0FE&color=2F6FED"
                     alt="${esc(p.mascota)}">
                <div class="vc-list-info">
                    <strong>${esc(p.cliente)}</strong>
                    <span>${esc(p.mascota)} &bull; ${esc(p.raza)}</span>
                </div>
                <div class="vc-list-meta">
                    <span class="time">${esc(p.hora)}</span>
                    <span class="vc-badge-pill ${esc(p.estado)}">${esc(estadoTxt.charAt(0).toUpperCase() + estadoTxt.slice(1))}</span>
                </div>
            </div>`;
        }).join('');
    }

    function seleccionarDia(fecha, citaId = null) {
        fechaSel = fecha;
        citaSel = citaId;
        calendar.select(fecha);   // marca el día en el calendario
        renderLista();
    }

    const calendar = new FullCalendar.Calendar(document.getElementById('fullcalendar'), {
        locale: 'es',
        initialView: 'dayGridMonth',   // única vista
        headerToolbar: false,          // usamos nuestras flechas y título
        height: 'auto',
        firstDay: 1,
        fixedWeekCount: false,
        selectable: true,
        unselectAuto: false,
        selectAllow: () => false,      // sin arrastrar; solo selección por clic
        eventDisplay: 'none',          // las citas NO se dibujan en el calendario
        events: {
            url: @json(route('dashboard.citas.eventos')),
            failure: () => {
                listEl.innerHTML = '<p class="text-danger small mb-0">No se pudieron cargar las citas.</p>';
            }
        },
        datesSet: (info) => {
            document.getElementById('calTitle').textContent = info.view.title;
        },
        eventsSet: renderLista,        // se repinta al cargar/cambiar los datos
        dateClick: (info) => seleccionarDia(info.dateStr.slice(0, 10)),
    });

    calendar.render();
    calendar.select(hoy);

    // Lista -> Calendario
    listEl.addEventListener('click', (e) => {
        const item = e.target.closest('.vc-list-item');
        if (!item) return;
        const ev = calendar.getEventById(item.dataset.id);
        if (!ev) return;
        calendar.gotoDate(ev.start);
        seleccionarDia(ev.extendedProps.fecha, ev.id);
    });

    document.getElementById('calPrev').addEventListener('click', () => calendar.prev());
    document.getElementById('calNext').addEventListener('click', () => calendar.next());
});
</script>
@endpush

{{-- ===================== GRÁFICA DE VENTAS ===================== --}}
@push('js')
<script>
    const ctxVentas = document.getElementById('chartVentas');

    const ventasData = @json($ventasUltimos5Meses->pluck('total') ?? []);
    const ventasLabels = @json($ventasUltimos5Meses->pluck('mes')->map(function ($mes) {
        return \Carbon\Carbon::createFromFormat('Y-m', $mes)->translatedFormat('M');
    }) ?? []);

    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ventasLabels.length ? ventasLabels : ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Ventas (COP)',
                data: ventasData.length ? ventasData : [8000000, 15000000, 22000000, 28000000, 38000000],
                borderColor: '#2F6FED',
                backgroundColor: 'rgba(47,111,237,0.08)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#2F6FED',
                pointRadius: 4,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'start',
                    labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, color: '#64748B', font: { size: 12 } }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => (value / 1000000) + 'M',
                        color: '#8996AC',
                    },
                    grid: { color: '#EEF1F6' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#8996AC' }
                }
            }
        }
    });
</script>
@endpush