@extends('layouts.app')

@section('subtitle', 'Reportes')

@section('content_body')

<br>

<div class="vc-page-header">
    <div class="vc-page-header__left">
        <span class="vc-page-icon"><i class="fas fa-chart-bar"></i></span>
        <div>
            <h1>Reportes</h1>
            <p>Informes de ventas, compras, inventario y atención de la clínica</p>
        </div>
    </div>
</div>

{{-- ===================== TIPO DE REPORTE ===================== --}}
<div class="rp-tipos" id="rpTipos">
    @foreach ($tipos as $clave => $t)
        <button type="button"
                class="rp-tipo {{ $loop->first ? 'is-active' : '' }}"
                data-tipo="{{ $clave }}"
                data-fechas="{{ $t['fechas'] ? '1' : '0' }}">
            <span class="rp-tipo__icon"><i class="fas {{ $t['icono'] }}"></i></span>
            <span class="rp-tipo__txt">
                <strong>{{ $t['nombre'] }}</strong>
                <small>{{ $t['desc'] }}</small>
            </span>
        </button>
    @endforeach
</div>

{{-- ===================== FILTROS ===================== --}}
<div class="vc-card rp-filtros">
    <div class="vc-card__body">
        <div class="rp-filtros__fila">
            <div class="rp-campo">
                <label for="rpDesde">Desde</label>
                <input type="date" id="rpDesde" class="form-control">
            </div>
            <div class="rp-campo">
                <label for="rpHasta">Hasta</label>
                <input type="date" id="rpHasta" class="form-control">
            </div>

            <div class="rp-rangos" id="rpRangos">
                <button type="button" data-rango="hoy">Hoy</button>
                <button type="button" data-rango="7">7 días</button>
                <button type="button" data-rango="mes">Este mes</button>
                <button type="button" data-rango="mes_ant">Mes anterior</button>
                <button type="button" data-rango="anio">Este año</button>
            </div>

            <div class="rp-acciones">
                <button type="button" class="btn btn-primary" id="rpGenerar">
                    <i class="fas fa-sync-alt"></i> Generar
                </button>
                <button type="button" class="btn btn-danger" id="rpPdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button type="button" class="btn btn-success" id="rpCsv">
                    <i class="fas fa-file-excel"></i> Excel (CSV)
                </button>
            </div>
        </div>
        <small class="text-muted" id="rpNotaFechas" style="display:none;">
            Este reporte muestra el estado actual y no usa el rango de fechas.
        </small>
    </div>
</div>

<div id="rpEstado"></div>

{{-- ===================== RESUMEN ===================== --}}
<div class="rp-resumen" id="rpResumen"></div>

{{-- ===================== TABLA ===================== --}}
<div class="vc-card">
    <div class="vc-card__header rp-tabla__head">
        <span class="vc-card__title"><i class="fas fa-table"></i> <span id="rpTitulo">Reporte</span></span>
        <input type="search" id="rpBuscar" class="form-control form-control-sm rp-buscar" placeholder="Buscar en el reporte...">
    </div>
    <div class="vc-card__body" style="padding:0;">
        <div class="rp-scroll">
            <table class="rp-tabla" id="rpTabla">
                <thead></thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
        <div class="rp-pie" id="rpPie"></div>
    </div>
</div>

{{-- ===================== HISTORIAL ===================== --}}
<div class="vc-card">
    <div class="vc-card__header">
        <span class="vc-card__title"><i class="fas fa-history"></i> Reportes generados recientemente</span>
    </div>
    <div class="vc-card__body" style="padding:0;">
        <div class="rp-scroll">
            <table class="rp-tabla" id="rpHistorial">
                <thead>
                    <tr>
                        <th>Fecha</th><th>Reporte</th><th>Período</th><th>Formato</th>
                        <th class="rp-r">Registros</th><th>Generado por</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="rp-vacio">Cargando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<br>

@stop

@push('css')
<style>
    .rp-tipos { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .rp-tipo {
        display: flex; align-items: center; gap: 12px; text-align: left;
        background: #fff; border: 1px solid #E5EAF2; border-radius: 12px; padding: 12px 14px;
        cursor: pointer; transition: border-color .15s, box-shadow .15s, transform .15s;
    }
    .rp-tipo:hover { border-color: #2F6FED; box-shadow: 0 4px 14px rgba(47,111,237,.12); transform: translateY(-1px); }
    .rp-tipo.is-active { border-color: #2F6FED; background: #F3F7FF; box-shadow: inset 3px 0 0 #2F6FED; }
    .rp-tipo__icon {
        width: 40px; height: 40px; flex: 0 0 40px; border-radius: 10px;
        background: #E8F0FE; color: #2F6FED; display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .rp-tipo.is-active .rp-tipo__icon { background: #2F6FED; color: #fff; }
    .rp-tipo__txt strong { display: block; font-size: 13px; color: #1e293b; line-height: 1.2; }
    .rp-tipo__txt small { display: block; font-size: 11px; color: #64748b; margin-top: 2px; line-height: 1.25; }

    .rp-filtros { margin-bottom: 16px; }
    .rp-filtros__fila { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; }
    .rp-campo label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .rp-campo .form-control { min-width: 160px; }
    .rp-rangos { display: flex; flex-wrap: wrap; gap: 6px; }
    .rp-rangos button {
        border: 1px solid #D5DCE8; background: #fff; color: #475569; border-radius: 999px;
        padding: 5px 12px; font-size: 12px; cursor: pointer;
    }
    .rp-rangos button:hover { background: #F3F7FF; border-color: #2F6FED; color: #2F6FED; }
    .rp-acciones { display: flex; gap: 8px; margin-left: auto; flex-wrap: wrap; }
    .rp-filtros.sin-fechas .rp-campo, .rp-filtros.sin-fechas .rp-rangos { opacity: .45; pointer-events: none; }

    .rp-resumen { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 14px; margin-bottom: 16px; }
    .rp-kpi {
        background: #fff; border: 1px solid #E5EAF2; border-radius: 12px; padding: 14px 16px;
        border-left: 4px solid var(--c, #2F6FED);
    }
    .rp-kpi__label { font-size: 12px; color: #64748b; }
    .rp-kpi__valor { font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 2px; }

    .rp-tabla__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .rp-buscar { max-width: 260px; }
    .rp-scroll { overflow-x: auto; }
    .rp-tabla { width: 100%; border-collapse: collapse; font-size: 13px; }
    .rp-tabla th {
        background: #F1F5F9; color: #475569; font-size: 12px; text-align: left; padding: 10px 12px;
        border-bottom: 1px solid #E2E8F0; white-space: nowrap; user-select: none;
    }
    .rp-tabla th.rp-sort { cursor: pointer; }
    .rp-tabla th.rp-sort:hover { color: #2F6FED; }
    .rp-tabla td { padding: 9px 12px; border-bottom: 1px solid #EEF1F6; color: #1e293b; }
    .rp-tabla tbody tr:hover { background: #F8FAFF; }
    .rp-tabla tfoot td { font-weight: 700; background: #F8FAFC; border-top: 2px solid #CBD5E1; }
    .rp-r { text-align: right !important; }
    .rp-vacio { text-align: center; color: #94a3b8; padding: 28px 12px !important; }
    .rp-pie { padding: 8px 14px; font-size: 12px; color: #64748b; border-top: 1px solid #EEF1F6; }

    .rp-badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #E2E8F0; color: #475569; }
    .rp-badge.ok   { background: #DCFCE7; color: #15803d; }
    .rp-badge.warn { background: #FEF3C7; color: #b45309; }
    .rp-badge.bad  { background: #FEE2E2; color: #b91c1c; }
    .rp-badge.info { background: #DBEAFE; color: #1d4ed8; }

    @media (max-width: 767px) {
        .rp-acciones { margin-left: 0; width: 100%; }
        .rp-campo, .rp-campo .form-control { width: 100%; }
    }
</style>
@endpush

@push('js')
<script>
(function () {
    if (window.__reportesIniciado) return;
    window.__reportesIniciado = true;

    const URLS = {
        data:      @json(route('reportes.data')),
        pdf:       @json(route('reportes.pdf')),
        csv:       @json(route('reportes.csv')),
        historial: @json(route('reportes.historial')),
    };

    const $  = (s) => document.querySelector(s);
    const $$ = (s) => Array.from(document.querySelectorAll(s));

    let tipo  = (document.querySelector('.rp-tipo.is-active') || {}).dataset?.tipo || 'ventas';
    let datos = null;
    let orden = { k: null, asc: true };

    const COLORES = ['#2F6FED', '#22C55E', '#8B5CF6', '#F59E0B', '#14B8A6', '#EF4444'];

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    const iso = (d) => d.toLocaleDateString('en-CA');

    function fmt(v, t) {
        if (t === 'moneda')     return '$ ' + Number(v || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
        if (t === 'numero')     { const n = Number(v || 0); return n.toLocaleString('es-CO', { maximumFractionDigits: Number.isInteger(n) ? 0 : 2 }); }
        if (t === 'porcentaje') return Number(v || 0).toLocaleString('es-CO', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
        return v === null || v === undefined || v === '' ? '—' : String(v);
    }

    const esNumerico = (t) => t === 'moneda' || t === 'numero' || t === 'porcentaje';

    function claseEstado(txt) {
        const e = String(txt || '').toLowerCase();
        if (['pagada', 'confirmada', 'normal', 'recibida', 'completada', 'activo'].includes(e)) return 'ok';
        if (['pendiente', 'bajo'].includes(e)) return 'warn';
        if (['anulada', 'cancelada', 'agotado', 'anulado'].includes(e)) return 'bad';
        if (e.includes('atenci')) return 'info';
        return '';
    }

    function params(extra) {
        return new URLSearchParams(Object.assign({
            tipo: tipo,
            desde: $('#rpDesde').value,
            hasta: $('#rpHasta').value,
        }, extra || {}));
    }

    function mostrarError(msg) {
        $('#rpEstado').innerHTML =
            '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> ' + esc(msg) + '</div>';
    }
    function limpiarEstado() { $('#rpEstado').innerHTML = ''; }

    /* ---------------- generar ---------------- */
    async function generar(registrar) {
        const btn = $('#rpGenerar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Generando...';
        limpiarEstado();

        try {
            const res = await fetch(URLS.data + '?' + params({ registrar: registrar ? 1 : 0 }), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const texto = await res.text();
            let json = null;
            try { json = JSON.parse(texto); } catch (e) { /* respuesta no JSON */ }

            if (!res.ok || !json || !json.success) {
                console.error('[Reportes] HTTP ' + res.status, texto.slice(0, 800));
                throw new Error((json && json.message) || ('Error ' + res.status + ' al generar el reporte.'));
            }

            datos = json.reporte;
            orden = { k: null, asc: true };
            $('#rpBuscar').value = '';
            render();
            if (registrar) cargarHistorial();
        } catch (e) {
            mostrarError(e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Generar';
        }
    }

    /* ---------------- render ---------------- */
    function render() {
        if (!datos) return;
        $('#rpTitulo').textContent = datos.titulo || 'Reporte';

        if (datos.aviso) {
            $('#rpEstado').innerHTML = '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> ' + esc(datos.aviso) + '</div>';
        }

        $('#rpResumen').innerHTML = (datos.resumen || []).map((r, i) => `
            <div class="rp-kpi" style="--c:${COLORES[i % COLORES.length]}">
                <div class="rp-kpi__label">${esc(r.label)}</div>
                <div class="rp-kpi__valor">${esc(fmt(r.valor, r.tipo))}</div>
            </div>`).join('');

        renderTabla();
    }

    function renderTabla() {
        const cols = datos.columnas || [];
        const thead = $('#rpTabla thead');
        const tbody = $('#rpTabla tbody');
        const tfoot = $('#rpTabla tfoot');

        if (!cols.length) {
            thead.innerHTML = '';
            tfoot.innerHTML = '';
            tbody.innerHTML = '<tr><td class="rp-vacio">Sin datos para mostrar.</td></tr>';
            $('#rpPie').textContent = '';
            return;
        }

        thead.innerHTML = '<tr>' + cols.map((c) => {
            const flecha = orden.k === c.k ? (orden.asc ? ' ▲' : ' ▼') : '';
            return `<th class="rp-sort ${esNumerico(c.t) ? 'rp-r' : ''}" data-k="${esc(c.k)}">${esc(c.l)}${flecha}</th>`;
        }).join('') + '</tr>';

        const q = $('#rpBuscar').value.trim().toLowerCase();
        let filas = (datos.filas || []).slice();

        if (q) {
            filas = filas.filter((f) => cols.some((c) => fmt(f[c.k], c.t).toLowerCase().includes(q)));
        }

        if (orden.k) {
            const col = cols.find((c) => c.k === orden.k);
            filas.sort((a, b) => {
                const x = a[orden.k], y = b[orden.k];
                const r = esNumerico(col && col.t)
                    ? (Number(x) || 0) - (Number(y) || 0)
                    : String(x ?? '').localeCompare(String(y ?? ''), 'es', { numeric: true });
                return orden.asc ? r : -r;
            });
        }

        tbody.innerHTML = filas.length
            ? filas.map((f) => '<tr>' + cols.map((c) => {
                const v = f[c.k];
                if (c.t === 'estado') {
                    return `<td><span class="rp-badge ${claseEstado(v)}">${esc(fmt(v, 'texto'))}</span></td>`;
                }
                return `<td class="${esNumerico(c.t) ? 'rp-r' : ''}">${esc(fmt(v, c.t))}</td>`;
            }).join('') + '</tr>').join('')
            : '<tr><td colspan="' + cols.length + '" class="rp-vacio">No hay datos para el período seleccionado.</td></tr>';

        const tot = datos.totales || {};
        tfoot.innerHTML = Object.keys(tot).length
            ? '<tr>' + cols.map((c, i) => {
                if (i === 0) return '<td>TOTAL</td>';
                return `<td class="${esNumerico(c.t) ? 'rp-r' : ''}">${tot[c.k] !== undefined ? esc(fmt(tot[c.k], c.t)) : ''}</td>`;
            }).join('') + '</tr>'
            : '';

        const total = (datos.filas || []).length;
        $('#rpPie').textContent = filas.length === total
            ? total + ' registro(s)'
            : filas.length + ' de ' + total + ' registro(s)';
    }

    /* ---------------- historial ---------------- */
    async function cargarHistorial() {
        const tb = $('#rpHistorial tbody');
        try {
            const res = await fetch(URLS.historial, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            const json = await res.json();
            const filas = json.data || [];

            tb.innerHTML = filas.length
                ? filas.map((h) => `
                    <tr>
                        <td>${esc(h.fecha)}</td>
                        <td>${esc(h.nombre)}</td>
                        <td>${esc(h.rango)}</td>
                        <td><span class="rp-badge">${esc(h.formato)}</span></td>
                        <td class="rp-r">${esc(h.registros)}</td>
                        <td>${esc(h.usuario)}</td>
                        <td class="rp-r">
                            <button type="button" class="btn btn-sm btn-outline-primary rp-repetir"
                                    data-tipo="${esc(h.tipo)}" data-desde="${esc(h.desde || '')}" data-hasta="${esc(h.hasta || '')}">
                                <i class="fas fa-redo"></i> Repetir
                            </button>
                        </td>
                    </tr>`).join('')
                : '<tr><td colspan="7" class="rp-vacio">Aún no se han generado reportes.</td></tr>';
        } catch (e) {
            console.error('[Reportes] historial', e);
            tb.innerHTML = '<tr><td colspan="7" class="rp-vacio">No se pudo cargar el historial.</td></tr>';
        }
    }

    /* ---------------- selección de tipo y rangos ---------------- */
    function seleccionarTipo(t) {
        tipo = t;
        $$('.rp-tipo').forEach((b) => b.classList.toggle('is-active', b.dataset.tipo === t));
        const usaFechas = (document.querySelector('.rp-tipo[data-tipo="' + t + '"]') || {}).dataset?.fechas !== '0';
        document.querySelector('.rp-filtros').classList.toggle('sin-fechas', !usaFechas);
        $('#rpNotaFechas').style.display = usaFechas ? 'none' : 'block';
    }

    function aplicarRango(r) {
        const hoy = new Date();
        let d = new Date(hoy), h = new Date(hoy);

        if (r === '7')            d.setDate(d.getDate() - 6);
        else if (r === 'mes')     d = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        else if (r === 'mes_ant') { d = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1); h = new Date(hoy.getFullYear(), hoy.getMonth(), 0); }
        else if (r === 'anio')    d = new Date(hoy.getFullYear(), 0, 1);

        $('#rpDesde').value = iso(d);
        $('#rpHasta').value = iso(h);
    }

    /* ---------------- eventos ---------------- */
    document.addEventListener('click', (e) => {
        const bt = e.target.closest('.rp-tipo');
        if (bt) { seleccionarTipo(bt.dataset.tipo); generar(false); return; }

        const rg = e.target.closest('#rpRangos button');
        if (rg) { aplicarRango(rg.dataset.rango); generar(false); return; }

        const th = e.target.closest('#rpTabla th.rp-sort');
        if (th && datos) {
            orden = { k: th.dataset.k, asc: orden.k === th.dataset.k ? !orden.asc : true };
            renderTabla();
            return;
        }

        const rep = e.target.closest('.rp-repetir');
        if (rep) {
            seleccionarTipo(rep.dataset.tipo);
            if (rep.dataset.desde) $('#rpDesde').value = rep.dataset.desde;
            if (rep.dataset.hasta) $('#rpHasta').value = rep.dataset.hasta;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            generar(false);
        }
    });

    $('#rpGenerar').addEventListener('click', () => generar(true));

    $('#rpPdf').addEventListener('click', () => {
        window.open(URLS.pdf + '?' + params(), '_blank');
        setTimeout(cargarHistorial, 1500);
    });

    $('#rpCsv').addEventListener('click', () => {
        window.location.href = URLS.csv + '?' + params();
        setTimeout(cargarHistorial, 1500);
    });

    $('#rpBuscar').addEventListener('input', () => { if (datos) renderTabla(); });
    $('#rpDesde').addEventListener('change', () => generar(false));
    $('#rpHasta').addEventListener('change', () => generar(false));

    /* ---------------- inicio ---------------- */
    aplicarRango('mes');
    seleccionarTipo(tipo);
    generar(false);
    cargarHistorial();
})();
</script>
@endpush