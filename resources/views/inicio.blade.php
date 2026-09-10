@extends('layouts.app')

{{-- El layout arma el <title> como "{config('adminlte.title')} | {subtitle}" --}}
@section('title', 'Inicio')

@section('content_body')

<br>

 <div class="vc-page-header">
        <div class="vc-page-header__left">
            <span class="vc-page-icon"><i class="far fa-calendar"></i></span>
            <div>
                <h1>inicio</h1>
               <p>Tablero de control de ínformacion</p>
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
        <!-- Agenda -->
        <div class="vc-card">
            <div class="vc-card__header">
                <span class="vc-card__title"><i class="far fa-calendar"></i> Agenda de Citas</span>
                <div class="vc-cal-toggle">
                    <button type="button">Día</button>
                    <button type="button">Semana</button>
                    <button type="button" class="is-active">Mes</button>
                </div>
            </div>
            <div class="vc-card__body">
                <div class="vc-cal-nav">
                    <button type="button"><i class="fas fa-chevron-left"></i></button>
                    <strong>{{ ucfirst($calendario['nombreMes'] ?? 'Mayo') }}</strong>
                    <button type="button"><i class="fas fa-chevron-right"></i></button>
                </div>

                <div class="vc-cal-grid">
                    @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dow)
                        <div class="vc-cal-dow">{{ $dow }}</div>
                    @endforeach

                    @foreach ($calendario['semanas'] ?? [] as $semana)
                        @foreach ($semana as $dia)
                            <div class="vc-cal-day {{ !$dia['esDelMes'] ? 'is-muted' : '' }} {{ $dia['esHoy'] ? 'is-today' : '' }}">
                                {{ $dia['dia'] }}
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div class="vc-cal-legend">
                    <span><i style="background: var(--vc-green)"></i> Confirmada</span>
                    <span><i style="background: var(--vc-orange)"></i> Pendiente</span>
                    <span><i style="background: var(--vc-blue)"></i> En Atención</span>
                    <span><i style="background: var(--vc-red)"></i> Cancelada</span>
                </div>
            </div>
        </div>

        <!-- Próximas Citas -->
        <div class="vc-card">
            <div class="vc-card__header">
                <span class="vc-card__title"><i class="far fa-calendar"></i> Próximas Citas</span>
            </div>
            <div class="vc-card__body">
                <div class="vc-list">
                    @forelse ($proximasCitas ?? [] as $cita)
                        <div class="vc-list-item">
                            <img class="vc-list-avatar"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($cita->mascota->nombre) }}&background=E8F0FE&color=2F6FED"
                                 alt="{{ $cita->mascota->nombre }}">
                            <div class="vc-list-info">
                                <strong>{{ $cita->cliente->nombres ?? '' }}</strong>
                                <span>{{ $cita->mascota->nombre }} &bull; {{ $cita->mascota->raza }}</span>
                            </div>
                            <div class="vc-list-meta">
                                <span class="time">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('h:i A') }}</span>
                                <span class="vc-badge-pill {{ $cita->estado }}">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No hay citas próximas.</p>
                    @endforelse
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