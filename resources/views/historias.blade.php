@extends('layouts.app')

@section('titulo', 'Historias Clínicas - VetFlow')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-notes-medical mr-2"></i>Historias Clínicas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('mascotas') }}">mascotas</a></li>
                <li class="breadcrumb-item active">Historias Clínicas</li>
            </ol>
        </div>
    </div>
@stop

{{-- ============================================================ --}}
{{-- CSS (solo se carga en esta vista)                             --}}
{{-- ============================================================ --}}
@section('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body .content-wrapper { background: #F4F6FB; }

    .hc-page {
        --hc-blue: #2563EB;
        --hc-border: #E2E8F0;
        --hc-muted: #64748B;
        --hc-soft: #F8FAFC;
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        color: #0F172A;
        font-size: 13px;
    }
    .hc-page *, .hc-page *::before, .hc-page *::after { box-sizing: border-box; }

    /* ---------- Título ---------- */
    .hc-title { display: flex; align-items: center; gap: 12px; margin: 2px 0 14px; }
    .hc-title a { color: #0F172A; font-size: 18px; line-height: 1; }
    .hc-title h1 { font-size: 19px; font-weight: 600; margin: 0; }

    /* ---------- Rejilla principal ---------- */
    .hc-grid { display: grid; grid-template-columns: minmax(0, 1fr) 318px; gap: 16px; align-items: start; }

    .hc-card {
        background: #fff;
        border: 1px solid #EEF1F6;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
    }

    /* ---------- Botones ---------- */
    .hc-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        height: 34px; padding: 0 16px;
        border-radius: 7px; border: 1px solid #D5DCE8; background: #fff;
        color: #1E293B; font-size: 13px; font-weight: 500;
        cursor: pointer; text-decoration: none; white-space: nowrap;
    }
    .hc-btn:hover { background: #F8FAFC; color: #0F172A; text-decoration: none; }
    .hc-btn--primary { background: var(--hc-blue); border-color: var(--hc-blue); color: #fff; }
    .hc-btn--primary:hover { background: #1D4ED8; color: #fff; }
    .hc-btn--outline-blue { border-color: #93B8F5; color: var(--hc-blue); font-weight: 600; font-size: 12.5px; }
    .hc-btn--outline-blue:hover { background: #EFF5FF; color: var(--hc-blue); }
    .hc-btn--icon { width: 34px; padding: 0; }
    .hc-btn--green { background: #16A34A; border-color: #16A34A; color: #fff; }
    .hc-btn--green:hover { background: #15803D; color: #fff; }
    .hc-btn[disabled] { opacity: .55; cursor: not-allowed; }
    .hc-btn[hidden] { display: none !important; }

    /* ---------- Cabecera de la mascota ---------- */
    .hc-pet { display: flex; align-items: center; gap: 16px; padding: 14px 20px; }
    .hc-avatar {
        width: 66px; height: 66px; border-radius: 50%; flex: 0 0 66px;
        object-fit: cover; background: #E2E8F0; color: #94A3B8; font-size: 26px;
        display: flex; align-items: center; justify-content: center;
    }
    .hc-pet-main { flex: 1 1 auto; min-width: 0; }
    .hc-pet-name { font-size: 21px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 10px; }
    .hc-pet-name .sex-m { color: #3B82F6; font-size: 16px; }
    .hc-pet-name .sex-f { color: #EC4899; font-size: 16px; }
    .hc-pet-sub { color: var(--hc-muted); font-size: 13px; margin-top: 6px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .hc-pill { display: inline-block; padding: 2px 12px; border-radius: 6px; background: #DCFCE7; color: #16A34A; font-size: 12px; font-weight: 500; }
    .hc-pill--off { background: #E2E8F0; color: #475569; }
    .hc-pill--dead { background: #1E293B; color: #fff; }
    .hc-meta { display: flex; gap: 44px; margin-right: 8px; }
    .hc-meta small { display: block; color: var(--hc-muted); font-size: 12px; margin-bottom: 6px; }
    .hc-meta span { font-size: 13px; font-weight: 500; }
    .hc-pet-actions { display: flex; align-items: center; gap: 10px; }

    /* ---------- Pestañas ---------- */
    .hc-tabs {
        display: flex;
        flex-wrap: wrap;               /* si no caben, bajan de línea: sin barra de scroll */
        gap: 0 4px;
        border-top: 1px solid #EEF1F6;
        padding: 0 16px;
        overflow: visible;
    }
    .hc-tabs a {
        flex: 0 0 auto;
        padding: 14px 12px 13px; color: #475569; font-size: 13px; white-space: nowrap;
        border-bottom: 2px solid transparent; text-decoration: none;
    }
    .hc-tabs a:hover { color: var(--hc-blue); }
    .hc-tabs a.is-active { color: var(--hc-blue); font-weight: 500; border-bottom-color: var(--hc-blue); }

    /* Paneles de las pestañas */
    [data-panel][hidden] { display: none !important; }

    /* ---------- Consulta ---------- */
    .hc-consulta { margin-top: 14px; padding: 20px 24px 22px; }
    .hc-consulta-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: nowrap; margin-bottom: 18px; }
    .hc-consulta-title { min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hc-consulta-actions { flex: 0 0 auto; align-items: center; }
    .hc-btn--pdf i { color: #EF4444; font-size: 15px; }
    .hc-consulta-title { font-size: 16px; font-weight: 600; margin: 0; }
    .hc-consulta-actions { display: flex; gap: 10px; }

    .hc-cols { display: grid; grid-template-columns: 1fr 1.12fr; gap: 28px; }
    .hc-block { margin-bottom: 16px; }
    .hc-label { font-size: 12.5px; font-weight: 500; color: #1E293B; margin: 0 0 6px; display: block; }
    .hc-box {
        border: 1px solid var(--hc-border); border-radius: 6px; background: #fff;
        padding: 10px 12px; font-size: 13px; line-height: 1.5; color: #1E293B;
        white-space: pre-line;
    }
    .hc-box ul { margin: 0; padding-left: 18px; line-height: 1.75; }

    /* ---------- Signos vitales (compactos y responsive) ---------- */
    .hc-vitals {
        --hc-vital-w: 76px;
        display: flex;
        flex-wrap: wrap;               /* baja de línea si no caben */
        gap: 12px 16px;
    }
    .hc-vital { flex: 0 0 auto; }
    .hc-vital small { display: block; font-size: 11px; color: var(--hc-muted); margin-bottom: 4px; white-space: nowrap; }

    /* El contenedor del input no lleva borde propio: solo queda el borde del input */
    .hc-vital .hc-inwrap { position: relative; border: 0; padding: 0; background: transparent; border-radius: 0; }
    .hc-vital > .hc-inwrap,
    .hc-vital > input.hc-input { width: var(--hc-vital-w); }
    .hc-vital > select.hc-input { width: 104px; }       /* "Moderada" necesita más espacio */

    .hc-vital .hc-input { padding: 7px 6px; font-size: 12px; text-align: center; font-weight: 600; }
    .hc-vital .hc-inwrap .hc-input { padding-right: 28px; }
    .hc-vital select.hc-input { text-align: left; font-weight: 400; padding: 7px 8px; }
    .hc-inwrap em { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-style: normal; font-size: 11px; color: #94A3B8; pointer-events: none; }

    .hc-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid var(--hc-border); border-radius: 6px; overflow: hidden; font-size: 12px; }
    .hc-table th { background: var(--hc-soft); color: var(--hc-muted); font-weight: 500; padding: 8px 10px; text-align: left; border-bottom: 1px solid var(--hc-border); }
    .hc-table td { padding: 8px 10px; border-bottom: 1px solid #EEF1F6; color: #1E293B; }
    .hc-table tr:last-child td { border-bottom: 0; }

    .hc-divider { border-top: 1px solid #EEF1F6; margin: 4px -24px 18px; }
    .hc-bottom { display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px; }
    .hc-next { display: flex; align-items: center; gap: 10px; color: #1E293B; }
    .hc-next i { color: #64748B; font-size: 15px; }

    /* ---------- Archivos / adjuntos ---------- */
    .hc-files { display: flex; flex-wrap: wrap; gap: 12px; }
    .hc-file-img { width: 160px; height: 128px; border-radius: 6px; object-fit: cover; background: #0F172A; display: block; }
    .hc-file-doc {
        width: 188px; height: 128px; border: 1px solid var(--hc-border); background: var(--hc-soft);
        border-radius: 6px; display: flex; align-items: center; gap: 10px; padding: 12px; text-decoration: none; color: #1E293B;
    }
    .hc-file-doc i { font-size: 34px; color: #EF4444; }
    .hc-file-doc strong { display: block; font-size: 11.5px; font-weight: 600; line-height: 1.3; }
    .hc-file-doc span { display: block; font-size: 11px; color: var(--hc-muted); margin-top: 3px; }
    .hc-file-up {
        flex: 1 1 190px; min-width: 190px; height: 128px; margin: 0;
        border: 1.5px dashed #C7D2E5; border-radius: 6px; background: #FBFCFE;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;
        color: var(--hc-muted); font-size: 11.5px; text-align: center; cursor: pointer;
    }
    .hc-file-up i { font-size: 18px; }
    .hc-file-up b { font-weight: 500; color: #1E293B; font-size: 12px; }

    /* ---------- Columna derecha ---------- */
    .hc-side { display: flex; flex-direction: column; gap: 16px; }
    .hc-side-card { padding: 16px 18px; }
    .hc-side-title { font-size: 14px; font-weight: 600; margin: 0 0 14px; display: flex; align-items: center; justify-content: space-between; }
    .hc-side-title a { font-size: 11.5px; font-weight: 500; color: var(--hc-blue); }

    .hc-info { display: flex; gap: 14px; align-items: flex-start; }
    .hc-info .hc-avatar { width: 60px; height: 60px; flex-basis: 60px; font-size: 22px; }
    .hc-kv { display: grid; grid-template-columns: 76px 1fr; row-gap: 9px; margin: 0; font-size: 12px; flex: 1; min-width: 0; }
    .hc-kv dt { color: var(--hc-muted); font-weight: 400; }
    .hc-kv dd { margin: 0; color: #1E293B; word-break: break-word; }

    .hc-owner { display: flex; gap: 14px; align-items: flex-start; }
    .hc-owner > i { color: #64748B; font-size: 24px; width: 34px; text-align: center; margin-top: 2px; }
    .hc-owner-name { font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .hc-owner ul { list-style: none; margin: 0; padding: 0; }
    .hc-owner li { display: flex; gap: 9px; align-items: flex-start; font-size: 11.5px; color: #475569; margin-bottom: 7px; word-break: break-word; }
    .hc-owner li i { width: 13px; text-align: center; color: #64748B; margin-top: 2px; }

    .hc-tl { position: relative; list-style: none; margin: 0; padding: 0 0 0 22px; }
    .hc-tl::before { content: ''; position: absolute; left: 4px; top: 14px; bottom: 14px; width: 2px; background: #DBEAFE; }
    .hc-tl li { position: relative; padding: 9px 12px; margin-bottom: 8px; border-radius: 6px; }
    .hc-tl li::before { content: ''; position: absolute; left: -22px; top: 15px; width: 9px; height: 9px; border-radius: 50%; background: var(--hc-blue); }
    .hc-tl li.is-current { background: #EAF2FE; }
    .hc-tl li.is-current::before { background: #22C55E; }
    .hc-tl .t-date { font-size: 11.5px; color: var(--hc-muted); }
    .hc-tl .t-title { font-size: 12px; font-weight: 500; color: #1E293B; margin: 3px 0; }
    .hc-tl .t-doc { font-size: 11.5px; color: var(--hc-muted); }
    .hc-tl-more { display: block; text-align: center; margin-top: 10px; font-size: 12px; font-weight: 500; color: var(--hc-blue); }

    /* ---------- Campos editables ---------- */
    .hc-input {
        display: block; width: 100%;
        border: 1px solid var(--hc-border); border-radius: 6px; background: #fff;
        padding: 10px 12px; font: inherit; font-size: 13px; line-height: 1.5; color: #1E293B;
        transition: border-color .15s, box-shadow .15s;
    }
    .hc-input::placeholder { color: #94A3B8; }
    .hc-input:focus { outline: 0; border-color: var(--hc-blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
    textarea.hc-input { resize: vertical; }
    select.hc-input { height: auto; }
    .hc-input--plain, .hc-input--plain:focus { border: 0; padding: 0; background: transparent; box-shadow: none; }
    .hc-table td { padding: 6px 8px; }
    .hc-table td .hc-input { padding: 6px 8px; font-size: 12px; }
    .hc-row-del { border: 0; background: transparent; color: #94A3B8; cursor: pointer; padding: 4px; }
    .hc-row-del:hover { color: #EF4444; }
    .hc-btn--sm { height: 30px; padding: 0 12px; font-size: 12px; }
    .hc-box.hc-next { padding: 8px 12px; }

    /* ---------- Panel Resumen ---------- */
    .hc-resumen { margin-top: 14px; padding: 20px 24px 22px; }
    .hc-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .hc-stat { background: var(--hc-soft); border: 1px solid #EEF1F6; border-radius: 10px; padding: 12px 14px; }
    .hc-stat small { display: block; color: var(--hc-muted); font-size: 11.5px; margin-bottom: 6px; }
    .hc-stat strong { font-size: 17px; font-weight: 600; color: #0F172A; }
    .hc-alert { background: #FEF3C7; color: #92400E; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px; }
    .hc-alert i { margin-right: 6px; }
    .hc-mini { display: grid; grid-template-columns: repeat(auto-fit, minmax(92px, 1fr)); gap: 10px; }
    .hc-mini div { background: var(--hc-soft); border: 1px solid #EEF1F6; border-radius: 8px; padding: 8px 10px; }
    .hc-mini small { display: block; font-size: 11px; color: var(--hc-muted); margin-bottom: 3px; }
    .hc-mini b { font-size: 13px; font-weight: 600; color: #1E293B; }

    /* ---------- Listados: exámenes, vacunas, desparasitaciones, recetas ---------- */
    .hc-exam { border: 1px solid var(--hc-border); border-radius: 10px; padding: 14px 16px; margin-bottom: 12px; }
    .hc-exam__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .hc-exam__head strong { font-size: 14px; }
    .hc-exam__date { color: var(--hc-muted); font-size: 12px; margin-top: 2px; }
    .hc-exam .hc-box { margin-top: 10px; }
    .hc-exam__obs { margin-top: 8px; font-size: 12px; color: var(--hc-muted); white-space: pre-line; }
    .hc-exam__file { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; font-size: 12px; color: var(--hc-blue); }
    .hc-exam__chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .hc-exam__next { margin-top: 10px; font-size: 12px; font-weight: 500; color: #16A34A; }
    .hc-exam__next.is-late { color: #DC2626; }

    .hc-chip { background: var(--hc-soft); border: 1px solid #EEF1F6; border-radius: 6px; padding: 3px 9px; font-size: 11.5px; color: #475569; }
    .hc-chip--aplicada, .hc-chip--activa       { background: #DCFCE7; color: #16A34A; border-color: transparent; }
    .hc-chip--programada                       { background: #DBEAFE; color: #2563EB; border-color: transparent; }
    .hc-chip--cancelada, .hc-chip--vencida     { background: #E2E8F0; color: #475569; border-color: transparent; }

    .hc-rx-med { font-size: 12.5px; margin-top: 8px; color: #1E293B; }
    .hc-rx-med small { display: block; color: var(--hc-muted); }

    /* Botones de acción de cada receta (ver, editar, imprimir, eliminar) */
    .hc-rx-actions { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
    .hc-ico { display: inline-flex; width: 26px; height: 26px; align-items: center; justify-content: center; border: 0; border-radius: 6px; color: #fff; font-size: 12px; cursor: pointer; text-decoration: none; }
    .hc-ico:hover { color: #fff; filter: brightness(.92); text-decoration: none; }
    .hc-ico--blue { background: #2563EB; }
    .hc-ico--gray { background: #64748B; }
    .hc-ico--red  { background: #EF4444; }

    /* ---------- Modal de receta ---------- */
    .hc-rx-icon { display: inline-flex; width: 30px; height: 30px; align-items: center; justify-content: center; border-radius: 8px; background: #2563EB; color: #fff; margin-right: 10px; font-size: 14px; }
    .hc-rx-pet { display: flex; gap: 14px; align-items: center; background: #F3F6FB; border: 1px solid #E6ECF5; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; }
    .hc-rx-pet .hc-avatar { width: 72px; height: 72px; flex-basis: 72px; }
    .hc-rx-pet h6 { margin: 0 0 4px; font-size: 16px; font-weight: 600; }
    .hc-rx-pet h6 .sex-m { color: #3B82F6; }
    .hc-rx-pet h6 .sex-f { color: #EC4899; }
    .hc-rx-pet p { margin: 0; font-size: 12.5px; color: #475569; line-height: 1.6; }
    .hc-rx-box { border: 1px solid #EEF1F6; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; }
    .hc-rx-title { font-size: 14px; font-weight: 600; margin-bottom: 10px; }
    .hc-rx-grid { display: grid; grid-template-columns: minmax(150px, 2fr) minmax(90px, 1fr) minmax(110px, 1fr) minmax(80px, .8fr) minmax(150px, 2fr) 28px; gap: 10px; align-items: start; }
    .hc-rx-cols { background: var(--hc-soft); border-radius: 6px; padding: 8px 10px; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .hc-rx-row { margin-bottom: 10px; }
    .hc-rx-row .hc-input { padding: 7px 9px; font-size: 12px; }
    .hc-rx-row textarea.hc-input { resize: none; }
    .hc-rx-del { border: 0; background: transparent; color: #EF4444; cursor: pointer; padding: 6px 0; font-size: 14px; }
    fieldset.rx-campos { border: 0; padding: 0; margin: 0; min-width: 0; }
    .is-readonly .hc-rx-del { display: none; }
    .is-readonly .hc-input:disabled { background: #F8FAFC; color: #334155; opacity: 1; }

    @@media (max-width: 767px) {
        .hc-tabs {
            flex-wrap: nowrap;         /* una sola fila */
            overflow-x: auto;          /* el scroll horizontal aparece solo en pantallas pequeñas */
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .hc-rx-cols { display: none; }
        .hc-rx-grid { grid-template-columns: 1fr 1fr; }
        .hc-rx-grid > :nth-child(1), .hc-rx-grid > :nth-child(5) { grid-column: 1 / -1; }
        .hc-rx-del { grid-column: 1 / -1; justify-self: end; }
    }
    @@media (max-width: 575px) {
        .hc-consulta-head { flex-wrap: wrap; }
        .hc-consulta-title { white-space: normal; }
    }
    @@media (max-width: 1199px) {
        .hc-grid { grid-template-columns: minmax(0, 1fr); }
        .hc-cols, .hc-bottom { grid-template-columns: minmax(0, 1fr); }
        .hc-pet { flex-wrap: wrap; }
    }
    @@media print {
        .hc-side, .hc-tabs, .hc-consulta-actions, .hc-pet-actions, .hc-title { display: none !important; }
        .hc-grid { grid-template-columns: minmax(0, 1fr); }
    }

    .hc-tl a.t-link { display: block; color: inherit; text-decoration: none; }

</style>
@stop

{{-- ============================================================ --}}
{{-- CONTENIDO                                                     --}}
{{-- ============================================================ --}}
@section('content_body')
@php
    /*
     * Variables que envía HistoriaClinicaController@show:
     *   mascota, historia, historial, examenes, vacunaciones, desparasitaciones,
     *   catalogoVacunas, catalogoDesparasitantes, recetas, veterinarios, medicamentosCatalogo
     */
    $m    = $mascota ?? null;
    $cli  = $m?->cliente;
    $h    = $historia ?? null;
    $hist = $historial ?? collect();

    $ruta = fn ($nombre, $param = []) => \Illuminate\Support\Facades\Route::has($nombre) ? route($nombre, $param) : '#';

    $fmtFecha = function ($valor, $conHora = true) {
        if (!$valor) return '—';
        $f = \Carbon\Carbon::parse($valor);
        $txt = $f->day . ' de ' . ucfirst($f->translatedFormat('F')) . ' de ' . $f->year;
        return $conHora ? $txt . ' - ' . $f->format('h:i A') : $txt;
    };

    // Convierte un valor (texto o array de ítems) en texto, una línea por ítem
    $aTexto = function ($v) {
        if (is_array($v)) {
            return implode("\n", array_map(
                fn ($i) => is_array($i) ? implode(' ', array_filter($i)) : (string) $i,
                $v
            ));
        }
        return (string) $v;
    };

    // Mascota
    $esMacho = str_starts_with(strtolower((string) ($m->genero ?? '')), 'm');
    $edad = null;
    if ($m?->fecha_nacimiento) {
        $nac  = \Carbon\Carbon::parse($m->fecha_nacimiento);
        $anos = $nac->age;
        $edad = $anos >= 1
            ? $anos . ($anos === 1 ? ' año' : ' años')
            : $nac->diffInMonths(now()) . ' meses';
    }
    $foto = $m?->foto ? asset('storage/' . $m->foto) : null;
    $estadoPill = [
        'activo'    => ['Activo',    ''],
        'inactivo'  => ['Inactivo',  'hc-pill--off'],
        'fallecido' => ['Fallecido', 'hc-pill--dead'],
    ][strtolower($m->estado ?? 'activo')] ?? ['Activo', ''];

    // Consulta
    $vital = fn ($valor, $unidad = '') => filled($valor) ? trim($valor . ' ' . $unidad) : '—';

    $formula  = $h?->formula ?? collect();     // fórmula anterior (solo lectura)
    $archivos = $h?->archivos ?? collect();

    // Consulta nueva (sin historia) = fecha y hora actuales; si hay historia, su propia fecha
    $esNueva       = !$h;
    $fechaConsulta = $h?->fecha ? \Carbon\Carbon::parse($h->fecha) : now();
    $val           = fn ($campo, $default = '') => old($campo, $h->{$campo} ?? $default);
    $proxima       = old('proxima_cita', $h?->proxima_cita ? \Carbon\Carbon::parse($h->proxima_cita)->format('Y-m-d\TH:i') : '');

    // Resumen
    $ultima   = $hist->sortByDesc('fecha')->first() ?? $h;
    $totalCon = $hist->count() ?: ($h ? 1 : 0);
    $nAdj     = $ultima ? ($ultima->archivos?->count() ?? 0) : 0;

    $tratResumen = array_values(array_filter(array_map(
        fn ($l) => trim(ltrim($l, "-•* \t")),
        preg_split('/\r\n|\r|\n/', $aTexto($ultima->tratamiento ?? ''))
    ), 'strlen'));

    // Recetas
    $frecuencias = ['c/4h', 'c/6h', 'c/8h', 'c/12h', 'c/24h', 'c/48h', 'Dosis única', 'Según necesidad'];
@endphp

<div class="hc-page">

    <div class="hc-grid">

        {{-- ====================== COLUMNA PRINCIPAL ====================== --}}
        <div class="hc-main">

            {{-- ===== Cabecera de la mascota + pestañas ===== --}}
            <div class="hc-card">
                <div class="hc-pet">
                    @if ($foto)
                        <img class="hc-avatar" src="{{ $foto }}" alt="{{ $m->nombre ?? '' }}">
                    @else
                        <div class="hc-avatar"><i class="fas fa-paw"></i></div>
                    @endif

                    <div class="hc-pet-main">
                        <h2 class="hc-pet-name">
                            {{ $m->nombre ?? '—' }}
                            <i class="fas {{ $esMacho ? 'fa-mars sex-m' : 'fa-venus sex-f' }}"></i>
                        </h2>
                        <div class="hc-pet-sub">
                            <span>{{ $m->raza ?? $m->especie ?? '—' }}</span>
                            @if ($edad) <span>•</span><span>{{ $edad }}</span> @endif
                            @if (filled($m->peso ?? null)) <span>•</span><span>{{ $m->peso }} kg</span> @endif
                            <span class="hc-pill {{ $estadoPill[1] }}">{{ $estadoPill[0] }}</span>
                        </div>
                    </div>

                    <div class="hc-meta">
                        <div><small>Propietario</small><span>{{ $cli?->nombreCompleto() ?? '—' }}</span></div>
                    </div>

                    <div class="hc-pet-actions">
                        <a class="hc-btn hc-btn--outline-blue" href="{{ $ruta('mascotas.show', $m->id ?? 0) }}">
                            <i class="fas fa-paw"></i> Ver perfil de mascota
                        </a>
                        <button type="button" class="hc-btn hc-btn--icon" title="Más opciones"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>

                <nav class="hc-tabs">
                    <a href="#resumen" data-tab="resumen">Resumen</a>
                    <a href="#historias" data-tab="historias" class="is-active">Historias Clínicas</a>
                    <a href="#vacunas" data-tab="vacunas">Vacunas</a>
                    <a href="#desparasitaciones" data-tab="desparasitaciones">Desparasitaciones</a>
                    <a href="#examenes" data-tab="examenes">Exámenes</a>
                    <a href="#laboratorio" data-tab="laboratorio">Laboratorio</a>
                    <a href="#archivos" data-tab="archivos">Archivos</a>
                </nav>
            </div>

            {{-- ===== Panel: Resumen ===== --}}
            <div data-panel="resumen" hidden>
                <div class="hc-card hc-resumen">
                    <h3 class="hc-consulta-title" style="margin-bottom:16px;">Resumen clínico</h3>

                    @if (filled($m->alergias ?? null))
                        <div class="hc-alert"><i class="fas fa-exclamation-triangle"></i> <b>Alergias:</b> {{ $m->alergias }}</div>
                    @endif

                    <div class="hc-stats">
                        <div class="hc-stat"><small>Consultas</small><strong>{{ $totalCon }}</strong></div>
                        <div class="hc-stat"><small>Última consulta</small>
                            <strong>{{ $ultima?->fecha ? \Carbon\Carbon::parse($ultima->fecha)->format('d/m/Y') : '—' }}</strong></div>
                        <div class="hc-stat"><small>Próxima cita sugerida</small>
                            <strong>{{ $ultima?->proxima_cita ? \Carbon\Carbon::parse($ultima->proxima_cita)->format('d/m/Y') : '—' }}</strong></div>
                        <div class="hc-stat"><small>Adjuntos (última consulta)</small><strong>{{ $nAdj }}</strong></div>
                    </div>

                    @if ($ultima)
                        <div class="hc-cols">
                            <div>
                                <div class="hc-block">
                                    <span class="hc-label">Motivo de la última consulta</span>
                                    <div class="hc-box">{{ $ultima->motivo ?: '—' }}</div>
                                </div>
                                <div class="hc-block">
                                    <span class="hc-label">Diagnóstico</span>
                                    <div class="hc-box">{{ $ultima->diagnostico ?: '—' }}</div>
                                </div>
                                <div class="hc-block">
                                    <span class="hc-label">Observaciones</span>
                                    <div class="hc-box">{{ $ultima->observaciones ?: '—' }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="hc-block">
                                    <span class="hc-label">Últimos signos vitales</span>
                                    <div class="hc-mini">
                                        <div><small>Temp.</small><b>{{ $vital($ultima->temperatura ?? null, '°C') }}</b></div>
                                        <div><small>F. card.</small><b>{{ $vital($ultima->frecuencia_cardiaca ?? null, 'lpm') }}</b></div>
                                        <div><small>F. resp.</small><b>{{ $vital($ultima->frecuencia_respiratoria ?? null, 'rpm') }}</b></div>
                                        <div><small>Peso</small><b>{{ $vital($ultima->peso ?? null, 'kg') }}</b></div>
                                        <div><small>Est. corporal</small><b>{{ $vital($ultima->condicion_corporal ?? null) }}</b></div>
                                        <div><small>Hidratación</small><b>{{ $vital($ultima->hidratacion ?? null) }}</b></div>
                                    </div>
                                </div>
                                <div class="hc-block">
                                    <span class="hc-label">Tratamiento</span>
                                    <div class="hc-box">
                                        @if ($tratResumen)
                                            <ul>@foreach ($tratResumen as $t)<li>{{ $t }}</li>@endforeach</ul>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aún no hay consultas registradas para esta mascota.</p>
                    @endif
                </div>
            </div>

            {{-- ===== Panel genérico para pestañas sin contenido todavía ===== --}}
            <div data-panel="vacio" hidden>
                <div class="hc-card hc-resumen">
                    <p class="text-muted mb-0"><b id="hcVacioTitulo"></b> aún no tiene información disponible.</p>
                </div>
            </div>

            {{-- ===== Paneles: Exámenes y Laboratorio ===== --}}
            @php $examenes = $examenes ?? collect(); @endphp

            @foreach ([
                'examenes'    => ['categoria' => 'examen',      'titulo' => 'Exámenes'],
                'laboratorio' => ['categoria' => 'laboratorio', 'titulo' => 'Laboratorio'],
            ] as $panel => $cfg)
                @php $items = $examenes->where('categoria', $cfg['categoria']); @endphp

                <div data-panel="{{ $panel }}" hidden>
                    <div class="hc-card hc-resumen">
                        <div class="hc-consulta-head">
                            <h3 class="hc-consulta-title">{{ $cfg['titulo'] }}</h3>
                            <button type="button" class="hc-btn hc-btn--primary js-nuevo-examen"
                                    data-categoria="{{ $cfg['categoria'] }}" data-titulo="{{ $cfg['titulo'] }}">
                                <i class="fas fa-plus"></i> Nuevo
                            </button>
                        </div>

                        <div class="hc-lista" data-lista="examen-{{ $cfg['categoria'] }}"
                             data-vacio="Aún no hay registros. Usa el botón Nuevo para agregar el primero.">
                            @forelse ($items as $e)
                                <div class="hc-exam" data-id="{{ $e->id }}">
                                    <div class="hc-exam__head">
                                        <div>
                                            <strong>{{ $e->nombre }}</strong>
                                            <div class="hc-exam__date">{{ $e->fecha->format('d/m/Y') }} · {{ $e->veterinario->name ?? '—' }}</div>
                                        </div>
                                        <button type="button" class="hc-row-del js-borrar-examen" title="Eliminar"
                                                data-url="{{ $ruta('examenes.destroy', $e->id) }}"><i class="far fa-trash-alt"></i></button>
                                    </div>

                                    @if ($e->resultado)
                                        <div class="hc-box">{{ $e->resultado }}</div>
                                    @endif
                                    @if ($e->observaciones)
                                        <div class="hc-exam__obs">{{ $e->observaciones }}</div>
                                    @endif
                                    @if ($e->archivo_ruta)
                                        <a class="hc-exam__file" href="{{ asset('storage/' . $e->archivo_ruta) }}" target="_blank" rel="noopener">
                                            <i class="far fa-file-alt"></i> {{ $e->archivo_nombre ?? 'Ver archivo' }}
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small mb-0 hc-vacio">Aún no hay registros. Usa el botón Nuevo para agregar el primero.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- ===== Paneles: Vacunas y Desparasitaciones ===== --}}
            @php
                $vacunaciones      = $vacunaciones ?? collect();
                $desparasitaciones = $desparasitaciones ?? collect();

                $paneles = [
                    'vacunas' => [
                        'tipo' => 'vacuna', 'titulo' => 'Vacunas', 'items' => $vacunaciones,
                        'rel' => 'vacuna', 'fk' => 'vacuna_id', 'borrar' => 'aplicaciones.vacuna.destroy',
                    ],
                    'desparasitaciones' => [
                        'tipo' => 'desparasitacion', 'titulo' => 'Desparasitaciones', 'items' => $desparasitaciones,
                        'rel' => 'desparasitante', 'fk' => 'desparasitante_id', 'borrar' => 'aplicaciones.desparasitacion.destroy',
                    ],
                ];

                // Productos del catálogo para el select del modal
                $catalogos = [
                    'vacuna' => ($catalogoVacunas ?? collect())->map(fn ($p) => [
                        'id' => $p->id, 'nombre' => $p->nombre, 'stock' => $p->stock,
                        'via' => $p->via_aplicacion, 'intervalo' => $p->intervalo_dias,
                    ])->values(),
                    'desparasitacion' => ($catalogoDesparasitantes ?? collect())->map(fn ($p) => [
                        'id' => $p->id, 'nombre' => $p->nombre, 'stock' => $p->stock,
                        'via' => $p->via_aplicacion, 'intervalo' => null,   // Desparasitante no tiene intervalo_dias
                    ])->values(),
                ];
            @endphp

            @foreach ($paneles as $panel => $cfg)
                <div data-panel="{{ $panel }}" hidden>
                    <div class="hc-card hc-resumen">
                        <div class="hc-consulta-head">
                            <h3 class="hc-consulta-title">{{ $cfg['titulo'] }}</h3>
                            <button type="button" class="hc-btn hc-btn--primary js-nueva-aplicacion"
                                    data-tipo="{{ $cfg['tipo'] }}" data-titulo="{{ $cfg['titulo'] }}">
                                <i class="fas fa-plus"></i> Nuevo
                            </button>
                        </div>

                        <div class="hc-lista" data-lista="aplicacion-{{ $cfg['tipo'] }}"
                             data-vacio="Aún no hay registros. Usa el botón Nuevo para agregar el primero.">
                            @forelse ($cfg['items'] as $ap)
                                @php
                                    $estado = $ap->estado ?: 'aplicada';
                                    $tardia = $ap->fecha_proxima && $estado !== 'cancelada' && $ap->fecha_proxima->lt(today());
                                @endphp
                                <div class="hc-exam" data-id="{{ $ap->id }}" data-producto="{{ $ap->{$cfg['fk']} }}"
                                     data-estado="{{ $estado }}" data-tipo="{{ $cfg['tipo'] }}">
                                    <div class="hc-exam__head">
                                        <div>
                                            <strong>{{ $ap->{$cfg['rel']}->nombre ?? '—' }}</strong>
                                            <div class="hc-exam__date">{{ $ap->fecha_aplicacion->format('d/m/Y') }} · {{ $ap->usuario->name ?? '—' }}</div>
                                        </div>
                                        <div class="d-flex align-items-center" style="gap:8px">
                                            <span class="hc-chip hc-chip--{{ $estado }}">{{ ucfirst($estado) }}</span>
                                            <button type="button" class="hc-row-del js-borrar-aplicacion" title="Eliminar"
                                                    data-url="{{ $ruta($cfg['borrar'], $ap->id) }}"><i class="far fa-trash-alt"></i></button>
                                        </div>
                                    </div>

                                    @if ($ap->via_aplicacion || $ap->lote || filled($ap->dosis_aplicada))
                                        <div class="hc-exam__chips">
                                            @if ($ap->via_aplicacion)         <span class="hc-chip">Vía: {{ ucfirst($ap->via_aplicacion) }}</span> @endif
                                            @if ($ap->lote)                   <span class="hc-chip">Lote: {{ $ap->lote }}</span> @endif
                                            @if (filled($ap->dosis_aplicada)) <span class="hc-chip">Dosis: {{ rtrim(rtrim((string) $ap->dosis_aplicada, '0'), '.') }}</span> @endif
                                        </div>
                                    @endif

                                    @if ($ap->fecha_proxima)
                                        <div class="hc-exam__next {{ $tardia ? 'is-late' : '' }}">
                                            <i class="far fa-calendar-alt"></i> Próxima: {{ $ap->fecha_proxima->format('d/m/Y') }}
                                            @if ($tardia) (vencida) @endif
                                        </div>
                                    @endif

                                    @if ($ap->observaciones)
                                        <div class="hc-exam__obs">{{ $ap->observaciones }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small mb-0 hc-vacio">Aún no hay registros. Usa el botón Nuevo para agregar el primero.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- ===== Panel: Historias Clínicas ===== --}}
            <div data-panel="historias">

                {{-- ===== Detalle de la consulta (formulario editable) ===== --}}
               <form id="formHistoria" method="POST" enctype="multipart/form-data" novalidate
                data-mascota="{{ $m->id ?? '' }}"
                data-peso-defecto="{{ $m->peso ?? '' }}"
                data-url-store="{{ $ruta('historias.store', ['mascota' => $m->id ?? 0]) }}"
                action="{{ $h ? $ruta('historias.update', $h->id) : $ruta('historias.store', ['mascota' => $m->id ?? 0]) }}">
                    @csrf
                    @if ($h) @method('PUT') @endif
                    <input type="hidden" name="mascota_id" value="{{ $m->id ?? '' }}">
                    <input type="hidden" name="fecha" id="hcFecha" value="{{ $fechaConsulta->format('Y-m-d H:i:s') }}">

                    <div class="hc-card hc-consulta">
                        <div class="hc-consulta-head">
                            <h3 class="hc-consulta-title">
                                Consulta del <span id="hcFechaTitulo" data-live="{{ $esNueva ? 1 : 0 }}">{{ $fmtFecha($fechaConsulta) }}</span>
                            </h3>
                            <div class="hc-consulta-actions">
                                <button type="button" class="hc-btn hc-btn--icon" onclick="window.print()" title="Imprimir" aria-label="Imprimir"><i class="fas fa-print"></i></button>
                                <a class="hc-btn hc-btn--icon hc-btn--pdf" href="{{ $ruta('historias.pdf', $h->id ?? 0) }}" title="Descargar PDF" aria-label="Descargar PDF"><i class="far fa-file-pdf"></i></a>
                               @if ($h)
                                    <a class="hc-btn" href="{{ $ruta('historias.show', [$m->id ?? 0, 'nueva' => 1]) }}"><i class="fas fa-plus"></i> Nueva consulta</a>
                               @endif
                                <button type="submit" class="hc-btn hc-btn--primary" id="hcGuardar"><i class="far fa-save"></i> Guardar</button>
                            </div>
                        </div>

                        <div class="hc-cols">
                            {{-- ----- Izquierda ----- --}}
                            <div>
                                <div class="hc-block">
                                    <label class="hc-label" for="hcMotivo">Motivo de consulta</label>
                                    <input type="text" id="hcMotivo" name="motivo" class="hc-input" value="{{ $val('motivo') }}" placeholder="Motivo de la consulta">
                                </div>

                                <div class="hc-block">
                                    <label class="hc-label" for="hcAnamnesis">Anamnesis</label>
                                    <textarea id="hcAnamnesis" name="anamnesis" rows="3" class="hc-input" placeholder="Historia y evolución del problema">{{ $val('anamnesis') }}</textarea>
                                </div>

                                <div class="hc-block">
                                    <span class="hc-label">Signos Vitales</span>
                                    <div class="hc-vitals">
                                        <div class="hc-vital" title="Temperatura"><small>Temp.</small>
                                            <div class="hc-inwrap">
                                                <input type="text" inputmode="decimal" name="temperatura" class="hc-input"
                                                       data-int="2" data-dec="1" autocomplete="off" value="{{ $val('temperatura') }}"><em>°C</em>
                                            </div>
                                        </div>
                                        <div class="hc-vital" title="Frecuencia cardíaca"><small>F. card.</small>
                                            <div class="hc-inwrap">
                                                <input type="text" inputmode="numeric" name="frecuencia_cardiaca" class="hc-input"
                                                       data-int="2" autocomplete="off" value="{{ $val('frecuencia_cardiaca') }}"><em>lpm</em>
                                            </div>
                                        </div>
                                        <div class="hc-vital" title="Frecuencia respiratoria"><small>F. resp.</small>
                                            <div class="hc-inwrap">
                                                <input type="text" inputmode="numeric" name="frecuencia_respiratoria" class="hc-input"
                                                       data-int="2" autocomplete="off" value="{{ $val('frecuencia_respiratoria') }}"><em>rpm</em>
                                            </div>
                                        </div>
                                        <div class="hc-vital" title="Peso"><small>Peso</small>
                                            <div class="hc-inwrap">
                                                <input type="text" inputmode="decimal" name="peso" class="hc-input"
                                                       data-int="2" data-dec="1" autocomplete="off" value="{{ $val('peso', $m->peso ?? '') }}"><em>kg</em>
                                            </div>
                                        </div>
                                        <div class="hc-vital"><small>Est. corporal</small>
                                            <input type="text" name="condicion_corporal" class="hc-input" maxlength="3"
                                                   value="{{ $val('condicion_corporal') }}" placeholder="4/9">
                                        </div>
                                        <div class="hc-vital"><small>Hidratación</small>
                                            <select name="hidratacion" class="hc-input">
                                                @foreach (['Normal', 'Leve', 'Moderada', 'Severa'] as $op)
                                                    <option value="{{ $op }}" @selected($val('hidratacion', 'Normal') === $op)>{{ $op }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="hc-block">
                                    <label class="hc-label" for="hcExamen">Examen Físico</label>
                                    <textarea id="hcExamen" name="examen_fisico" rows="3" class="hc-input" placeholder="Hallazgos del examen físico">{{ $val('examen_fisico') }}</textarea>
                                </div>
                            </div>

                            {{-- ----- Derecha ----- --}}
                            <div>
                                <div class="hc-block">
                                    <label class="hc-label" for="hcDiagnostico">Diagnóstico</label>
                                    <textarea id="hcDiagnostico" name="diagnostico" rows="2" class="hc-input" placeholder="Diagnóstico presuntivo o definitivo">{{ $val('diagnostico') }}</textarea>
                                </div>

                                <div class="hc-block">
                                    <label class="hc-label" for="hcTratamiento">Tratamiento</label>
                                    <textarea id="hcTratamiento" name="tratamiento" rows="4" class="hc-input" placeholder="Un ítem por línea">{{ old('tratamiento', $aTexto($h->tratamiento ?? '')) }}</textarea>
                                </div>

                                {{-- ===== Fórmula Médica: recetas ===== --}}
                                <div class="hc-block">
                                    <span class="hc-label">Fórmula Médica</span>

                                    <div class="hc-lista" id="hcRecetas" data-lista="receta"
                                         data-vacio="Aún no hay recetas para esta mascota.">
                                        @forelse (($recetas ?? collect()) as $rx)
                                            <div class="hc-exam" data-receta="{{ $rx->id }}">
                                                <div class="hc-exam__head">
                                                    <div>
                                                        <strong>Receta del {{ $rx->fecha_emision->format('d/m/Y') }}</strong>
                                                        <div class="hc-exam__date">{{ $rx->veterinario->name ?? '—' }}</div>
                                                    </div>
                                                    <div class="hc-rx-actions">
                                                        <span class="hc-chip hc-chip--{{ $rx->estado }}">{{ ucfirst($rx->estado) }}</span>
                                                        <button type="button" class="hc-ico hc-ico--blue js-ver-receta" title="Ver"
                                                                data-url="{{ $ruta('recetas.show', $rx->id) }}"><i class="far fa-eye"></i></button>
                                                        <button type="button" class="hc-ico hc-ico--blue js-editar-receta" title="Editar"
                                                                data-url="{{ $ruta('recetas.show', $rx->id) }}"><i class="far fa-edit"></i></button>
                                                        <a class="hc-ico hc-ico--gray" title="Imprimir" target="_blank" rel="noopener"
                                                           href="{{ $ruta('recetas.pdf', $rx->id) }}"><i class="fas fa-print"></i></a>
                                                        <button type="button" class="hc-ico hc-ico--red js-borrar-receta" title="Eliminar"
                                                                data-url="{{ $ruta('recetas.show', $rx->id) }}"><i class="far fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                                @foreach ($rx->medicamentos as $med)
                                                    <div class="hc-rx-med">
                                                        {{ $med->medicamento }} — {{ $med->dosis }}@if ($med->frecuencia) {{ $med->frecuencia }}@endif @if ($med->duracion_dias) · {{ $med->duracion_dias }} días @endif
                                                        @if ($med->instrucciones) <small>{{ $med->instrucciones }}</small> @endif
                                                    </div>
                                                @endforeach
                                                @if ($rx->indicaciones_generales)
                                                    <div class="hc-exam__obs">{{ $rx->indicaciones_generales }}</div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-muted small hc-vacio">Aún no hay recetas para esta mascota.</p>
                                        @endforelse
                                    </div>

                                   @if ($formula->count())
                                      <details id="hcFormulaAnterior" style="margin-top:8px;">
                                              <summary class="small text-muted" style="cursor:pointer;">Fórmula anterior de esta consulta</summary>
                                            <table class="hc-table" style="margin-top:6px;">
                                                <thead><tr><th>Producto</th><th>Presentación</th><th>Dosis</th><th>Cantidad</th></tr></thead>
                                                <tbody>
                                                    @foreach ($formula as $f)
                                                        <tr><td>{{ $f->producto }}</td><td>{{ $f->presentacion }}</td><td>{{ $f->dosis }}</td><td>{{ $f->cantidad }}</td></tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </details>
                                    @endif

                                    <button type="button" class="hc-btn hc-btn--sm hc-btn--green" id="hcAddReceta" style="margin-top:8px;" @disabled(!$m)>
                                        <i class="fas fa-plus"></i> Agregar receta
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="hc-divider"></div>

                        {{-- ----- Observaciones + próxima cita ----- --}}
                        <div class="hc-bottom">
                            <div class="hc-block">
                                <label class="hc-label" for="hcObs">Observaciones</label>
                                <textarea id="hcObs" name="observaciones" rows="2" class="hc-input" placeholder="Recomendaciones y controles">{{ $val('observaciones') }}</textarea>
                            </div>
                            <div class="hc-block">
                                <label class="hc-label" for="hcProxima">Próxima cita sugerida</label>
                                <div class="hc-box hc-next">
                                    <i class="far fa-calendar"></i>
                                    <input type="datetime-local" id="hcProxima" name="proxima_cita" class="hc-input hc-input--plain" value="{{ $proxima }}">
                                </div>
                            </div>
                        </div>

                        {{-- ----- Archivos / adjuntos ----- --}}
                        <div class="hc-block" style="margin-bottom:0;">
                            <span class="hc-label">Archivos / Adjuntos</span>
                            <div class="hc-files">
                            <div id="hcArchivosExistentes" style="display:contents;">
                                    @foreach ($archivos as $a)
                                    @php
                                        $nombreArchivo = $a->nombre ?? basename($a->ruta ?? '');
                                        $ext  = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
                                        $url  = asset('storage/' . ($a->ruta ?? ''));
                                        $peso = is_numeric($a->tamano ?? null) ? number_format($a->tamano / 1024, 0) . ' KB' : '';
                                    @endphp

                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $url }}" target="_blank" rel="noopener">
                                            <img class="hc-file-img" src="{{ $url }}" alt="{{ $nombreArchivo }}">
                                        </a>
                                    @else
                                        <a class="hc-file-doc" href="{{ $url }}" target="_blank" rel="noopener">
                                            <i class="far fa-file-pdf"></i>
                                            <div>
                                                <strong>{{ $a->titulo ?? pathinfo($nombreArchivo, PATHINFO_FILENAME) }}</strong>
                                                <span>{{ $nombreArchivo }}</span>
                                                @if ($peso) <span>{{ $peso }}</span> @endif
                                            </div>
                                        </a>
                                    @endif
                                  @endforeach
                                </div>

                                {{-- Vista previa de los archivos recién elegidos --}}
                                <div id="hcNuevos" style="display:contents;"></div>

                                <label class="hc-file-up" for="hcSubirArchivo">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <b>Subir archivo</b>
                                    <span>PDF, JPG, PNG (Máx. 10MB)</span>
                                    <input type="file" id="hcSubirArchivo" name="archivos[]" multiple accept=".pdf,.jpg,.jpeg,.png" hidden>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>{{-- /data-panel historias --}}
        </div>

        {{-- ====================== COLUMNA DERECHA ====================== --}}
        <aside class="hc-side">

            {{-- ===== Información de la mascota ===== --}}
            <div class="hc-card hc-side-card">
                <h4 class="hc-side-title">Información de la Mascota</h4>
                <div class="hc-info">
                    @if ($foto)
                        <img class="hc-avatar" src="{{ $foto }}" alt="{{ $m->nombre ?? '' }}">
                    @else
                        <div class="hc-avatar"><i class="fas fa-paw"></i></div>
                    @endif

                    <dl class="hc-kv">
                        <dt>Nombre</dt>       <dd>{{ $m->nombre ?? '—' }}</dd>
                        <dt>Especie</dt>      <dd>{{ ucfirst($m->especie ?? '—') }}</dd>
                        <dt>Raza</dt>         <dd>{{ $m->raza ?? '—' }}</dd>
                        <dt>Sexo</dt>         <dd>{{ ucfirst($m->genero ?? '—') }}</dd>
                        <dt>F. Nac.</dt>
                        <dd>
                            {{ $m?->fecha_nacimiento ? \Carbon\Carbon::parse($m->fecha_nacimiento)->format('d/m/Y') : '—' }}
                            @if ($edad) ({{ $edad }}) @endif
                        </dd>
                        <dt>Peso</dt>         <dd>{{ filled($m->peso ?? null) ? $m->peso . ' kg' : '—' }}</dd>
                        <dt>Color</dt>        <dd>{{ $m->color ?? '—' }}</dd>
                        <dt>Microchip</dt>    <dd>{{ $m->numero_chip ?? '—' }}</dd>
                        <dt>Alergias</dt>     <dd>{{ ($m->alergias ?? null) ?: 'Ninguna conocida' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- ===== Propietario ===== --}}
            <div class="hc-card hc-side-card">
                <h4 class="hc-side-title">
                    Propietario
                    <a href="{{ $ruta('clientes.show', $cli->id ?? 0) }}">Ver perfil</a>
                </h4>
                <div class="hc-owner">
                    <i class="fas fa-user-friends"></i>
                    <div>
                        <div class="hc-owner-name">{{ $cli?->nombreCompleto() ?? '—' }}</div>
                        <ul>
                            <li><i class="far fa-id-card"></i><span>{{ trim(($cli->tipo_documento ?? 'CC') . ' ' . ($cli->numero_documento ?? '')) }}</span></li>
                            <li><i class="fas fa-phone-alt"></i><span>{{ ($cli->celular ?? null) ?: ($cli->telefono ?? '—') }}</span></li>
                            <li><i class="far fa-envelope"></i><span>{{ $cli->email ?? '—' }}</span></li>
                            <li><i class="fas fa-map-marker-alt"></i><span>{{ collect([$cli->direccion ?? null, $cli->ciudad ?? null])->filter()->implode(', ') ?: '—' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ===== Historial clínico (línea de tiempo) ===== --}}
            <div class="hc-card hc-side-card">
                <h4 class="hc-side-title">Historial Clínico</h4>
              <ul class="hc-tl" id="hcTimeline">
                    @forelse ($hist as $item)
                        <li class="{{ ($item->id ?? null) === ($h->id ?? null) ? 'is-current' : '' }}">
                            <a class="t-link" href="{{ $ruta('historias.show', [$m->id ?? 0, 'consulta' => $item->id]) }}">
                                <div class="t-date">{{ $item->fecha ? \Carbon\Carbon::parse($item->fecha)->format('d/m/Y - h:i A') : '—' }}</div>
                                <div class="t-title">{{ $item->motivo ?? '—' }}</div>
                                <div class="t-doc">{{ $item->veterinario->name ?? '—' }}</div>
                            </a>
                        </li>
                    @empty
                        <li><div class="t-doc">Sin consultas registradas</div></li>
                    @endforelse
                </ul>
                <a class="hc-tl-more" href="{{ $ruta('historias.index') }}">Ver historial completo</a>
            </div>
        </aside>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODALES (fuera de .hc-page y fuera del formulario de consulta) --}}
{{-- ============================================================ --}}

{{-- ===== Modal: Exámenes y Laboratorio ===== --}}
<div class="modal fade hc-page" id="modalExamen" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formExamen" enctype="multipart/form-data"
          data-url="{{ $ruta('examenes.store', $m->id ?? 0) }}">
      @csrf
      <input type="hidden" name="categoria">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo registro · <span id="examenTitulo"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="hc-block">
          <label class="hc-label" for="exNombre">Nombre</label>
          <input type="text" id="exNombre" name="nombre" class="hc-input" maxlength="150" required
                 placeholder="Ej: Hemograma, Radiografía de tórax">
        </div>
        <div class="hc-block">
          <label class="hc-label" for="exFecha">Fecha</label>
          <input type="date" id="exFecha" name="fecha" class="hc-input" required>
        </div>
        <div class="hc-block">
          <label class="hc-label" for="exResultado">Resultado</label>
          <textarea id="exResultado" name="resultado" rows="3" class="hc-input"></textarea>
        </div>
        <div class="hc-block">
          <label class="hc-label" for="exObs">Observaciones</label>
          <textarea id="exObs" name="observaciones" rows="2" class="hc-input"></textarea>
        </div>
        <div class="hc-block mb-0">
          <label class="hc-label" for="exArchivo">Archivo (PDF, JPG, PNG · máx. 10 MB)</label>
          <input type="file" id="exArchivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="hc-btn" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="hc-btn hc-btn--primary"><i class="far fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== Modal: Vacunas y Desparasitaciones ===== --}}
<div class="modal fade hc-page" id="modalAplicacion" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formAplicacion"
          data-url-vacuna="{{ $ruta('aplicaciones.vacuna.store', $m->id ?? 0) }}"
          data-url-desparasitacion="{{ $ruta('aplicaciones.desparasitacion.store', $m->id ?? 0) }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Nueva aplicación · <span id="apTitulo"></span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="hc-block">
          <label class="hc-label" for="apProducto" id="apProductoLabel">Producto</label>
          <select id="apProducto" name="producto_id" class="hc-input" required></select>
        </div>

        <div class="form-row">
          <div class="hc-block col-6">
            <label class="hc-label" for="apFecha">Fecha de aplicación</label>
            <input type="date" id="apFecha" name="fecha_aplicacion" class="hc-input" required>
          </div>
          <div class="hc-block col-6">
            <label class="hc-label" for="apProxima">Próxima aplicación</label>
            <input type="date" id="apProxima" name="fecha_proxima" class="hc-input">
          </div>
        </div>

        <div class="form-row">
          <div class="hc-block col-6">
            <label class="hc-label" for="apDosis">Dosis aplicada</label>
            <input type="number" step="0.01" min="0" id="apDosis" name="dosis_aplicada" class="hc-input">
          </div>
          <div class="hc-block col-6">
            <label class="hc-label" for="apLote">Lote</label>
            <input type="text" id="apLote" name="lote" class="hc-input" maxlength="50">
          </div>
        </div>

        <div class="form-row">
          <div class="hc-block col-6">
            <label class="hc-label" for="apVia">Vía de aplicación</label>
            <input type="text" id="apVia" name="via_aplicacion" class="hc-input" maxlength="50" list="apVias" autocomplete="off">
            <datalist id="apVias">
              @foreach (\App\Models\Vacuna::VIAS_APLICACION + \App\Models\Desparasitante::VIAS_APLICACION as $clave => $texto)
                <option value="{{ $clave }}">{{ $texto }}</option>
              @endforeach
            </datalist>
          </div>
          <div class="hc-block col-6">
            <label class="hc-label" for="apCosto">Costo</label>
            <input type="number" step="0.01" min="0" id="apCosto" name="costo" class="hc-input">
          </div>
        </div>

        <div class="hc-block">
          <label class="hc-label" for="apEstado">Estado</label>
          <select id="apEstado" name="estado" class="hc-input">
            <option value="aplicada">Aplicada</option>
            <option value="programada">Programada</option>
            <option value="cancelada">Cancelada</option>
          </select>
          <small class="text-muted">Solo el estado "Aplicada" descuenta del inventario.</small>
        </div>

        <div class="hc-block mb-0">
          <label class="hc-label" for="apObs">Observaciones</label>
          <textarea id="apObs" name="observaciones" rows="2" class="hc-input"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="hc-btn" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="hc-btn hc-btn--primary"><i class="far fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== Modal: Receta médica (nueva, ver y editar) ===== --}}
<div class="modal fade hc-page" id="modalReceta" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" id="formReceta"
      data-url="{{ $ruta('recetas.store', $m->id ?? 0) }}"
      data-mascota="{{ $m->id ?? 0 }}">
      @csrf
      <input type="hidden" name="historia_clinica_id" value="{{ $h->id ?? '' }}">

      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center">
          <span class="hc-rx-icon"><i class="fas fa-pills"></i></span> <span id="rxTitulo">Nueva Receta Médica</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <fieldset id="rxCampos" class="rx-campos">

          {{-- Paciente --}}
          <div class="hc-rx-pet">
            @if ($foto)
              <img class="hc-avatar" src="{{ $foto }}" alt="{{ $m->nombre ?? '' }}">
            @else
              <div class="hc-avatar"><i class="fas fa-paw"></i></div>
            @endif
            <div>
              <h6>{{ $m->nombre ?? '—' }} <i class="fas {{ $esMacho ? 'fa-mars sex-m' : 'fa-venus sex-f' }}"></i></h6>
              <p>{{ ucfirst($m->especie ?? '—') }} · {{ $m->raza ?? '—' }}</p>
              <p>
                @if ($edad) Edad: {{ $edad }} @endif
                @if ($edad && filled($m->peso ?? null)) | @endif
                @if (filled($m->peso ?? null)) Peso: {{ $m->peso }} kg @endif
              </p>
              <p>Propietario: {{ $cli?->nombreCompleto() ?? '—' }}</p>
            </div>
          </div>

          {{-- Fecha y médico --}}
          <div class="form-row">
            <div class="hc-block col-md-6">
              <label class="hc-label" for="rxFecha">Fecha de emisión <span class="text-danger">*</span></label>
              <input type="date" id="rxFecha" name="fecha_emision" class="hc-input" required>
            </div>
            <div class="hc-block col-md-6">
              <label class="hc-label" for="rxMedico">Médico veterinario <span class="text-danger">*</span></label>
              <select id="rxMedico" name="veterinario_id" class="hc-input" required>
                @foreach (($veterinarios ?? collect()) as $vet)
                  <option value="{{ $vet->id }}" @selected($vet->id === auth()->id())>{{ $vet->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Medicamentos --}}
          <div class="hc-rx-box">
            <div class="hc-rx-title">Medicamentos</div>

            <div class="hc-rx-grid hc-rx-cols">
              <span>Medicamento <span class="text-danger">*</span></span>
              <span>Dosis <span class="text-danger">*</span></span>
              <span>Frecuencia</span>
              <span>Duración</span>
              <span>Instrucciones</span>
              <span></span>
            </div>

            <div id="rxFilas"></div>

            <button type="button" class="hc-btn hc-btn--primary hc-btn--sm" id="rxAddFila">
              <i class="fas fa-plus"></i> Agregar medicamento
            </button>

            <datalist id="rxMedicamentos">
              @foreach (($medicamentosCatalogo ?? collect()) as $nombreMed)
                <option value="{{ $nombreMed }}"></option>
              @endforeach
            </datalist>
          </div>

          <div class="hc-block">
            <label class="hc-label" for="rxIndicaciones">Indicaciones generales</label>
            <textarea id="rxIndicaciones" name="indicaciones_generales" rows="2" class="hc-input"
                      placeholder="Reposo, hidratación, controles..."></textarea>
          </div>

          <div class="hc-block mb-0">
            <label class="hc-label" for="rxObs">Observaciones <small class="text-muted">(opcional)</small></label>
            <textarea id="rxObs" name="observaciones" rows="2" class="hc-input" placeholder="Observaciones adicionales..."></textarea>
          </div>
        </fieldset>
      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="hc-btn" data-dismiss="modal" id="rxCancelar">Cancelar</button>
        <div class="d-flex" style="gap:8px">
        <a id="rxImprimir" class="hc-btn" href="#" target="_blank" rel="noopener" hidden>
            <i class="fas fa-print"></i> Imprimir
        </a>
         <button type="button" id="rxPreview" class="hc-btn" hidden>
            <i class="far fa-eye"></i> Vista previa
        </button>
        <button type="submit" class="hc-btn hc-btn--primary" id="rxGuardar">
            <i class="far fa-save"></i> Guardar Receta
        </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ===== Modal: Vista previa de la receta (PDF embebido) ===== --}}
<div class="modal fade hc-page" id="modalRecetaPreview" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center">
          <span class="hc-rx-icon"><i class="far fa-file-pdf"></i></span>
          Vista previa de la receta
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body p-0" style="background:#F4F6FB;">
        <iframe id="rxPreviewFrame" src="about:blank"
                style="width:100%; height:75vh; border:0; display:block;"></iframe>
      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="hc-btn" data-dismiss="modal">Cerrar</button>
        <div class="d-flex" style="gap:8px">
          <a id="rxPreviewDownload" class="hc-btn" href="#" download>
            <i class="fas fa-download"></i> Descargar
          </a>
          <button type="button" class="hc-btn hc-btn--primary" id="rxPreviewPrint">
            <i class="fas fa-print"></i> Imprimir
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@stop

{{-- ============================================================ --}}
{{-- JS                                                            --}}
{{-- ============================================================ --}}

@push('js')
<script>
(function () {
    // Cada módulo corre aislado: si uno falla, los demás (pestañas, botones) siguen funcionando
    function modulo(nombre, fn) {
        try { fn(); } catch (e) { console.error('[historias] falló el módulo "' + nombre + '":', e); }
    }

    // ---------- Utilidades compartidas (listas sin recargar la página) ----------
    const hc = window.hc = {
        esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        },
        lista(clave) {
            return document.querySelector('.hc-lista[data-lista="' + clave + '"]');
        },
        agregar(clave, html) {
            const l = this.lista(clave);
            if (!l) return;
            const vacio = l.querySelector('.hc-vacio');
            if (vacio) vacio.remove();
            l.insertAdjacentHTML('afterbegin', html);
        },
        quitar(card) {
            const l = card.closest('.hc-lista');
            card.remove();
            if (l && !l.querySelector('.hc-exam')) {
                const p = document.createElement('p');
                p.className = 'text-muted small mb-0 hc-vacio';
                p.textContent = l.dataset.vacio || 'Sin registros.';
                l.appendChild(p);
            }
        },
        limpiarLista(clave, textoVacio) {
            const l = this.lista(clave);
            if (!l) return;
            l.querySelectorAll('img').forEach(i => URL.revokeObjectURL(i.src));
            l.innerHTML = '';
            const p = document.createElement('p');
            p.className = 'text-muted small mb-0 hc-vacio';
            p.textContent = textoVacio || l.dataset.vacio || 'Sin registros.';
            l.appendChild(p);
        },
        aviso(titulo) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: titulo, showConfirmButton: false, timer: 2200 });
        },
        errores(json, defecto) {
            return json && json.errors
                ? Object.values(json.errors).flat().join('<br>')
                : ((json && json.message) || defecto);
        },
    };

    // ---------- Accesibilidad de los modales (foco) y modales apilados ----------
    modulo('foco', function () {
        let origen = null;

        $(document).on('show.bs.modal', '.modal', function () {
            origen = document.activeElement;
        });
        $(document).on('hide.bs.modal', '.modal', function () {
            if (document.activeElement && this.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
        $(document).on('hidden.bs.modal', '.modal', function () {
            // Si queda otro modal abierto debajo, conserva su scroll
            if (document.querySelector('.modal.show')) document.body.classList.add('modal-open');
            if (origen && document.body.contains(origen) && !origen.disabled) origen.focus();
            origen = null;
        });
    });

    // ---------- Signos vitales: solo dígitos, con el máximo de cifras en data-int / data-dec ----------
    modulo('vitales', function () { document.querySelectorAll('.hc-vital input[data-int]').forEach(el => {
        el.addEventListener('input', () => {
            const ent = Number(el.dataset.int) || 2;
            const dec = Number(el.dataset.dec) || 0;

            let v = el.value.replace(',', '.').replace(/[^\d.]/g, '');
            let [entero, ...resto] = v.split('.');
            entero = entero.slice(0, ent);

            el.value = (dec > 0 && resto.length)
                ? entero + '.' + resto.join('').slice(0, dec)
                : entero;
        });
    }); });

    // ---------- Pestañas (delegación: funciona aunque falle cualquier otro módulo) ----------
    modulo('pestanas', function () {
        function activar(nombre) {
            const tab = document.querySelector('.hc-tabs a[data-tab="' + nombre + '"]');
            if (!tab) return;

            document.querySelectorAll('.hc-tabs a[data-tab]').forEach(a => a.classList.toggle('is-active', a === tab));

            const paneles = document.querySelectorAll('[data-panel]');
            const destino = document.querySelector('[data-panel="' + nombre + '"]')
                         || document.querySelector('[data-panel="vacio"]');
            paneles.forEach(p => { p.hidden = (p !== destino); });

            const t = document.getElementById('hcVacioTitulo');
            if (destino && destino.dataset.panel === 'vacio' && t) t.textContent = tab.textContent.trim();
        }

        document.addEventListener('click', function (e) {
            const tab = e.target.closest('.hc-tabs a[data-tab]');
            if (!tab) return;
            e.preventDefault();
            activar(tab.dataset.tab);
            try { history.replaceState(null, '', '#' + tab.dataset.tab); } catch (_) {}
        });

        const inicial = location.hash.replace('#', '');
        if (inicial) activar(inicial);
    });

    // ---------- Exámenes y Laboratorio ----------
    modulo('examenes', function () {
        const modalEl = document.getElementById('modalExamen');
        const form = document.getElementById('formExamen');
        if (!modalEl || !form) return;

        $(modalEl).appendTo('body');
        const token = form.querySelector('input[name=_token]').value;

        function cardExamen(e) {
            return '<div class="hc-exam" data-id="' + hc.esc(e.id) + '">' +
                '<div class="hc-exam__head"><div>' +
                    '<strong>' + hc.esc(e.nombre) + '</strong>' +
                    '<div class="hc-exam__date">' + hc.esc(e.fecha) + ' · ' + hc.esc(e.medico) + '</div>' +
                '</div>' +
                '<button type="button" class="hc-row-del js-borrar-examen" title="Eliminar" data-url="' + hc.esc(e.url_borrar) + '">' +
                    '<i class="far fa-trash-alt"></i></button></div>' +
                (e.resultado ? '<div class="hc-box">' + hc.esc(e.resultado) + '</div>' : '') +
                (e.observaciones ? '<div class="hc-exam__obs">' + hc.esc(e.observaciones) + '</div>' : '') +
                (e.archivo_url
                    ? '<a class="hc-exam__file" href="' + hc.esc(e.archivo_url) + '" target="_blank" rel="noopener">' +
                      '<i class="far fa-file-alt"></i> ' + hc.esc(e.archivo_nombre || 'Ver archivo') + '</a>'
                    : '') +
            '</div>';
        }

        $(document).on('click', '.js-nuevo-examen', function () {
            form.reset();
            form.elements['categoria'].value = this.dataset.categoria;
            form.elements['fecha'].value = new Date().toLocaleDateString('en-CA');
            document.getElementById('examenTitulo').textContent = this.dataset.titulo;
            $(modalEl).modal('show');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const categoria = form.elements['categoria'].value;
            const btn = form.querySelector('[type=submit]');
            btn.disabled = true;

            try {
                const res = await fetch(form.dataset.url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    console.warn('Examen:', res.status, json);
                    Swal.fire('Error ' + res.status, hc.errores(json, 'No se pudo guardar.'), 'error');
                    return;
                }

                $(modalEl).modal('hide');
                hc.agregar('examen-' + categoria, cardExamen(json.registro));
                hc.aviso(json.message);
            } catch (err) {
                Swal.fire('Error', 'No se pudo guardar.', 'error');
            } finally {
                btn.disabled = false;
            }
        });

        $(document).on('click', '.js-borrar-examen', function () {
            const url = this.dataset.url;
            const card = this.closest('.hc-exam');

            Swal.fire({
                title: '¿Eliminar este registro?', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
            }).then(r => {
                if (!r.isConfirmed) return;
                fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(res => res.ok ? hc.quitar(card) : Swal.fire('Error', 'No se pudo eliminar.', 'error'));
            });
        });
    });

    // ---------- Vacunas y Desparasitaciones ----------
    modulo('aplicaciones', function () {
        const modalEl = document.getElementById('modalAplicacion');
        const form = document.getElementById('formAplicacion');
        if (!modalEl || !form) return;

        $(modalEl).appendTo('body');

        const UNIDADES = 1;   // igual a UNIDADES_POR_APLICACION de AplicacionMascotaController
        const catalogos = @json($catalogos ?? []);
        const token = form.querySelector('input[name=_token]').value;
        const sel = form.elements['producto_id'];
        const fecha = form.elements['fecha_aplicacion'];
        const proxima = form.elements['fecha_proxima'];
        const via = form.elements['via_aplicacion'];
        let tipo = 'vacuna';
        let proximaManual = false;

        // Mantiene el stock del select al día sin recargar
        function ajustarStock(t, productoId, delta) {
            const p = (catalogos[t] || []).find(x => String(x.id) === String(productoId));
            if (p && p.stock !== null) p.stock = Number(p.stock) + delta;
        }

        function cardAplicacion(a) {
            const chips = [];
            if (a.via)   chips.push('Vía: ' + a.via);
            if (a.lote)  chips.push('Lote: ' + a.lote);
            if (a.dosis) chips.push('Dosis: ' + a.dosis);

            return '<div class="hc-exam" data-id="' + hc.esc(a.id) + '" data-producto="' + hc.esc(a.producto_id) + '"' +
                ' data-estado="' + hc.esc(a.estado) + '" data-tipo="' + hc.esc(a.tipo) + '">' +
                '<div class="hc-exam__head"><div>' +
                    '<strong>' + hc.esc(a.producto) + '</strong>' +
                    '<div class="hc-exam__date">' + hc.esc(a.fecha) + ' · ' + hc.esc(a.usuario) + '</div>' +
                '</div><div class="d-flex align-items-center" style="gap:8px">' +
                    '<span class="hc-chip hc-chip--' + hc.esc(a.estado) + '">' + hc.esc(a.estado.charAt(0).toUpperCase() + a.estado.slice(1)) + '</span>' +
                    '<button type="button" class="hc-row-del js-borrar-aplicacion" title="Eliminar" data-url="' + hc.esc(a.url_borrar) + '">' +
                    '<i class="far fa-trash-alt"></i></button>' +
                '</div></div>' +
                (chips.length
                    ? '<div class="hc-exam__chips">' + chips.map(c => '<span class="hc-chip">' + hc.esc(c) + '</span>').join('') + '</div>'
                    : '') +
                (a.proxima
                    ? '<div class="hc-exam__next' + (a.tardia ? ' is-late' : '') + '"><i class="far fa-calendar-alt"></i> Próxima: ' +
                      hc.esc(a.proxima) + (a.tardia ? ' (vencida)' : '') + '</div>'
                    : '') +
                (a.observaciones ? '<div class="hc-exam__obs">' + hc.esc(a.observaciones) + '</div>' : '') +
            '</div>';
        }

        // Sugiere la próxima fecha con el intervalo en días del producto
        function sugerirProxima() {
            const dias = Number(sel.selectedOptions[0]?.dataset.intervalo);
            if (!dias || proximaManual || !fecha.value) return;
            const d = new Date(fecha.value + 'T00:00:00');
            d.setDate(d.getDate() + dias);
            proxima.value = d.toLocaleDateString('en-CA');
        }

        proxima.addEventListener('input', () => { proximaManual = true; });
        fecha.addEventListener('change', sugerirProxima);
        sel.addEventListener('change', () => {
            const opt = sel.selectedOptions[0];
            if (opt && opt.dataset.via && !via.value) via.value = opt.dataset.via;
            sugerirProxima();
        });

        $(document).on('click', '.js-nueva-aplicacion', function () {
            tipo = this.dataset.tipo;
            const lista = catalogos[tipo] || [];

            if (!lista.length) {
                Swal.fire('Catálogo vacío',
                    'Primero registra productos en el módulo de ' + (tipo === 'vacuna' ? 'vacunas' : 'desparasitantes') + '.',
                    'info');
                return;
            }

            form.reset();
            proximaManual = false;
            fecha.value = new Date().toLocaleDateString('en-CA');

            sel.innerHTML = lista.map(p =>
                '<option value="' + p.id + '" data-via="' + hc.esc(p.via ?? '') + '" data-intervalo="' + hc.esc(p.intervalo ?? '') + '"></option>'
            ).join('');
            // El nombre se asigna como texto para evitar inyección de HTML
            [...sel.options].forEach((o, i) => { o.textContent = lista[i].nombre + ' · stock: ' + lista[i].stock; });

            document.getElementById('apTitulo').textContent = this.dataset.titulo;
            document.getElementById('apProductoLabel').textContent = tipo === 'vacuna' ? 'Vacuna' : 'Desparasitante';

            sel.dispatchEvent(new Event('change'));
            $(modalEl).modal('show');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('[type=submit]');
            btn.disabled = true;

            try {
                const res = await fetch(tipo === 'vacuna' ? form.dataset.urlVacuna : form.dataset.urlDesparasitacion, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    console.warn('Aplicación:', res.status, json);
                    Swal.fire('Error ' + res.status, hc.errores(json, 'No se pudo guardar.'), 'error');
                    return;
                }

                const a = json.registro;
                $(modalEl).modal('hide');
                hc.agregar('aplicacion-' + a.tipo, cardAplicacion(a));
                if (a.estado === 'aplicada') ajustarStock(a.tipo, a.producto_id, -UNIDADES);

                if (json.aviso) {
                    Swal.fire('Stock bajo', json.aviso, 'warning');
                } else {
                    hc.aviso(json.message);
                }
            } catch (err) {
                Swal.fire('Error', 'No se pudo guardar.', 'error');
            } finally {
                btn.disabled = false;
            }
        });

        $(document).on('click', '.js-borrar-aplicacion', function () {
            const url = this.dataset.url;
            const card = this.closest('.hc-exam');

            Swal.fire({
                title: '¿Eliminar este registro?', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
            }).then(r => {
                if (!r.isConfirmed) return;
                fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(res => {
                        if (!res.ok) return Swal.fire('Error', 'No se pudo eliminar.', 'error');
                        // El servidor devolvió la unidad al inventario; se refleja en el select
                        if (card.dataset.estado === 'aplicada') ajustarStock(card.dataset.tipo, card.dataset.producto, UNIDADES);
                        hc.quitar(card);
                    });
            });
        });
    });

    // ---------- Vista previa del PDF de la receta (modal con visor) ----------
    modulo('preview', function () {
        const modalPreviewEl = document.getElementById('modalRecetaPreview');
        const frame          = document.getElementById('rxPreviewFrame');
        const btnPreview     = document.getElementById('rxPreview');
        const btnDownload    = document.getElementById('rxPreviewDownload');
        const btnPrint       = document.getElementById('rxPreviewPrint');
        if (!modalPreviewEl || !frame || !btnPreview || !btnDownload || !btnPrint) return;

        $(modalPreviewEl).appendTo('body');

        let urlVer = null;
        let urlDescarga = null;
        window.hcSetPreviewUrl = (ver, descarga) => { urlVer = ver; urlDescarga = descarga || ver; };

        btnPreview.addEventListener('click', () => {
            if (!urlVer) {
                Swal.fire('Sin receta', 'Guarda la receta primero para ver la vista previa.', 'info');
                return;
            }
            frame.src = urlVer;
            btnDownload.href = urlDescarga;
            $(modalPreviewEl).modal('show');
        });

        // Modal apilado: queda por encima del modal de la receta y de su fondo oscuro
        $(modalPreviewEl).on('shown.bs.modal', function () {
            this.style.zIndex = 1080;
            const fondos = document.querySelectorAll('.modal-backdrop');
            if (fondos.length) fondos[fondos.length - 1].style.zIndex = 1075;
        });
        $(modalPreviewEl).on('hidden.bs.modal', function () { this.style.zIndex = ''; });

        // Limpia el visor al cerrar para que no siga cargando
        $(modalPreviewEl).on('hidden.bs.modal', () => { frame.src = 'about:blank'; });

        btnPrint.addEventListener('click', () => {
            try {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            } catch (e) {
                window.open(urlVer, '_blank');   // si el navegador bloquea el acceso al visor
            }
        });
    });

    // ---------- Recetas: nueva, ver, editar, imprimir y eliminar ----------
    modulo('recetas', function () {
        const modalEl = document.getElementById('modalReceta');
        const form = document.getElementById('formReceta');
        if (!modalEl || !form) return;

        $(modalEl).appendTo('body');

        const token = form.querySelector('input[name=_token]').value;
        const filas = document.getElementById('rxFilas');
        const campos = document.getElementById('rxCampos');
        const titulo = document.getElementById('rxTitulo');
        const btnGuardar = document.getElementById('rxGuardar');
        const btnImprimir = document.getElementById('rxImprimir');
        const btnPreview = document.getElementById('rxPreview');
        const btnCancelar = document.getElementById('rxCancelar');
        const btnAddFila = document.getElementById('rxAddFila');
        const btnAbrir = document.getElementById('hcAddReceta');
        const frecuencias = @json($frecuencias ?? []);
        if (!filas || !campos || !titulo || !btnGuardar) { console.warn('[historias] faltan elementos del modal de receta'); return; }

        let modo = 'nuevo';        // nuevo | editar | ver
        let recetaUrl = null;

        // Mantiene los nombres medicamentos[0][campo], medicamentos[1][campo]... sin huecos
        function reindexar() {
            [...filas.children].forEach((fila, i) => {
                fila.querySelectorAll('[data-c]').forEach(el => {
                    el.name = 'medicamentos[' + i + '][' + el.dataset.c + ']';
                });
            });
        }

        function nuevaFila(d) {
            d = d || {};
            const div = document.createElement('div');
            div.className = 'hc-rx-grid hc-rx-row';
            div.innerHTML =
                '<input type="text" class="hc-input" data-c="medicamento" list="rxMedicamentos" maxlength="150" placeholder="Medicamento" autocomplete="off">' +
                '<input type="text" class="hc-input" data-c="dosis" maxlength="100" placeholder="Dosis">' +
                '<select class="hc-input" data-c="frecuencia"><option value="">Seleccionar...</option>' +
                    frecuencias.map(f => '<option value="' + hc.esc(f) + '">' + hc.esc(f) + '</option>').join('') + '</select>' +
                '<input type="number" class="hc-input" data-c="duracion_dias" min="1" max="365" placeholder="Días">' +
                '<textarea class="hc-input" data-c="instrucciones" rows="2" maxlength="255" placeholder="Instrucciones adicionales"></textarea>' +
                '<button type="button" class="hc-rx-del" title="Quitar medicamento"><i class="far fa-trash-alt"></i></button>';
            filas.appendChild(div);

            // Los valores se asignan como texto para evitar inyección de HTML
            ['medicamento', 'dosis', 'frecuencia', 'duracion_dias', 'instrucciones'].forEach(c => {
                div.querySelector('[data-c="' + c + '"]').value = d[c] ?? '';
            });
            reindexar();
        }

        function setModo(m, url) {
            modo = m;
            recetaUrl = url || null;
            const ver = (m === 'ver');

            campos.disabled = ver;
            modalEl.classList.toggle('is-readonly', ver);
            titulo.textContent = m === 'nuevo' ? 'Nueva Receta Médica' : (m === 'editar' ? 'Editar Receta Médica' : 'Receta Médica');
            btnGuardar.hidden = ver;
            if (btnAddFila) btnAddFila.hidden = ver;
            if (btnImprimir) btnImprimir.hidden = (m === 'nuevo');
            if (btnPreview) btnPreview.hidden = (m === 'nuevo');
            if (btnCancelar) btnCancelar.textContent = ver ? 'Cerrar' : 'Cancelar';
        }

        function abrirNueva() {
            form.reset();
            filas.innerHTML = '';
            nuevaFila();
            form.elements['fecha_emision'].value = new Date().toLocaleDateString('en-CA');
            setModo('nuevo');
            if (window.hcSetPreviewUrl) window.hcSetPreviewUrl(null, null);
            $(modalEl).modal('show');
        }

        async function abrirExistente(url, m) {
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('No se pudo cargar (' + res.status + ')');
                const r = (await res.json()).receta;

                form.reset();
                filas.innerHTML = '';
                (r.medicamentos.length ? r.medicamentos : [{}]).forEach(med => nuevaFila(med));

                form.elements['fecha_emision'].value = r.fecha_iso;
                form.elements['veterinario_id'].value = r.veterinario_id ?? '';
                form.elements['indicaciones_generales'].value = r.indicaciones ?? '';
                form.elements['observaciones'].value = r.observaciones ?? '';

                setModo(m, r.url);
                if (btnImprimir) btnImprimir.href = r.url_pdf_preview || r.url_pdf;   // se abre en el visor del navegador y desde ahí se imprime
                if (window.hcSetPreviewUrl) window.hcSetPreviewUrl(r.url_pdf_preview || r.url_pdf, r.url_pdf);
                $(modalEl).modal('show');
            } catch (err) {
                console.warn('Receta:', err);
                Swal.fire('Error', 'No se pudo cargar la receta.', 'error');
            }
        }

        // ----- Tarjeta de receta (mismo diseño que el Blade) -----
        function lineaMed(m) {
            let t = hc.esc(m.medicamento) + ' — ' + hc.esc(m.dosis);
            if (m.frecuencia) t += ' ' + hc.esc(m.frecuencia);
            if (m.duracion_dias) t += ' · ' + hc.esc(m.duracion_dias) + ' días';
            return '<div class="hc-rx-med">' + t +
                (m.instrucciones ? '<small>' + hc.esc(m.instrucciones) + '</small>' : '') + '</div>';
        }

        function cardReceta(r) {
            const estado = r.estado === 'activa' ? 'Activa' : 'Vencida';
            return '<div class="hc-exam" data-receta="' + hc.esc(r.id) + '">' +
                '<div class="hc-exam__head"><div>' +
                    '<strong>Receta del ' + hc.esc(r.fecha) + '</strong>' +
                    '<div class="hc-exam__date">' + hc.esc(r.medico) + '</div>' +
                '</div><div class="hc-rx-actions">' +
                    '<span class="hc-chip hc-chip--' + hc.esc(r.estado) + '">' + estado + '</span>' +
                    '<button type="button" class="hc-ico hc-ico--blue js-ver-receta" title="Ver" data-url="' + hc.esc(r.url) + '"><i class="far fa-eye"></i></button>' +
                    '<button type="button" class="hc-ico hc-ico--blue js-editar-receta" title="Editar" data-url="' + hc.esc(r.url) + '"><i class="far fa-edit"></i></button>' +
                    '<a class="hc-ico hc-ico--gray" title="Imprimir" target="_blank" rel="noopener" href="' + hc.esc(r.url_pdf) + '"><i class="fas fa-print"></i></a>' +
                    '<button type="button" class="hc-ico hc-ico--red js-borrar-receta" title="Eliminar" data-url="' + hc.esc(r.url) + '"><i class="far fa-trash-alt"></i></button>' +
                '</div></div>' +
                r.medicamentos.map(lineaMed).join('') +
                (r.indicaciones ? '<div class="hc-exam__obs">' + hc.esc(r.indicaciones) + '</div>' : '') +
            '</div>';
        }

        // Si la consulta todavía no está guardada, se anota la receta para vincularla al guardar la consulta
        function vincularPendiente(id) {
            const hist = document.getElementById('formHistoria');
            if (!hist || (form.elements['historia_clinica_id'] && form.elements['historia_clinica_id'].value)) return;
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'recetas_ids[]';
            inp.value = id;
            hist.appendChild(inp);
        }

        function desvincular(id) {
            document.querySelectorAll('#formHistoria input[name="recetas_ids[]"]').forEach(inp => {
                if (String(inp.value) === String(id)) inp.remove();
            });
        }

        // ----- Eventos -----
        if (btnAbrir) btnAbrir.addEventListener('click', abrirNueva);
        if (btnAddFila) btnAddFila.addEventListener('click', () => nuevaFila());

        filas.addEventListener('click', (e) => {
            const del = e.target.closest('.hc-rx-del');
            if (!del) return;
            const fila = del.closest('.hc-rx-row');
            if (filas.children.length === 1) {
                fila.querySelectorAll('[data-c]').forEach(el => { el.value = ''; });   // deja siempre una fila
            } else {
                fila.remove();
                reindexar();
            }
        });

        $(document).on('click', '.js-ver-receta',    function () { abrirExistente(this.dataset.url, 'ver'); });
        $(document).on('click', '.js-editar-receta', function () { abrirExistente(this.dataset.url, 'editar'); });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (modo === 'ver') return;

            const fd = new FormData(form);
            if (!fd.get('mascota_id') && form.dataset.mascota) fd.set('mascota_id', form.dataset.mascota);

            let endpoint = form.dataset.url;
            if (modo === 'editar') {
                endpoint = recetaUrl;
                fd.append('_method', 'PUT');          // Laravel lo enruta a update()
                fd.delete('historia_clinica_id');     // editar no cambia la consulta de la receta
            } else if (!fd.get('historia_clinica_id')) {
                fd.delete('historia_clinica_id');     // al crear sin consulta guardada, no se envía vacío
            }

            const btn = btnGuardar;
            btn.disabled = true;

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    console.warn('Receta:', res.status, json);
                    Swal.fire('Error ' + res.status, hc.errores(json, 'No se pudo guardar la receta.'), 'error');
                    return;
                }

                const r = json.receta;
                $(modalEl).modal('hide');

                if (modo === 'editar') {
                    const card = document.querySelector('.hc-exam[data-receta="' + r.id + '"]');
                    if (card) card.outerHTML = cardReceta(r);
                } else {
                    hc.agregar('receta', cardReceta(r));
                    vincularPendiente(r.id);
                }
                hc.aviso(json.message);
            } catch (err) {
                Swal.fire('Error', 'No se pudo guardar.', 'error');
            } finally {
                btn.disabled = false;
            }
        });

        $(document).on('click', '.js-borrar-receta', function () {
            const url = this.dataset.url;
            const card = this.closest('.hc-exam');

            Swal.fire({
                title: '¿Eliminar esta receta?',
                text: 'Se borrarán todos sus medicamentos.',
                icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar',
            }).then(r => {
                if (!r.isConfirmed) return;
                fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
                    .then(res => {
                        if (!res.ok) return Swal.fire('Error', 'No se pudo eliminar.', 'error');
                        desvincular(card.dataset.receta);
                        hc.quitar(card);
                    });
            });
        });
    });

    // ---------- Imprimir receta: abre el diálogo de impresión del navegador (sin pestaña nueva) ----------
    modulo('imprimir', function () {
        let marco = null;

        async function imprimirPdf(url, boton) {
            const html = boton ? boton.innerHTML : null;
            if (boton) boton.style.pointerEvents = 'none';

            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/pdf' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const blob = new Blob([await res.blob()], { type: 'application/pdf' });
                const blobUrl = URL.createObjectURL(blob);

                if (marco) { URL.revokeObjectURL(marco.dataset.blob); marco.remove(); }
                marco = document.createElement('iframe');
                marco.dataset.blob = blobUrl;
                marco.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden';
                marco.onload = () => {
                    try {
                        marco.contentWindow.focus();
                        marco.contentWindow.print();
                    } catch (e) {
                        window.open(url, '_blank');   // último recurso si el navegador bloquea la impresión
                    }
                };
                marco.src = blobUrl;
                document.body.appendChild(marco);
            } catch (err) {
                console.warn('Imprimir receta:', err);
                Swal.fire('Error', 'No se pudo preparar la receta para imprimir.', 'error');
            } finally {
                if (boton) { boton.style.pointerEvents = ''; if (html !== null) boton.innerHTML = html; }
            }
        }

        window.hcImprimirPdf = imprimirPdf;

        // Botón del modal (#rxImprimir) y el icono de imprimir de cada tarjeta de receta
        document.addEventListener('click', function (e) {
            const a = e.target.closest('#rxImprimir, .hc-rx-actions a[title="Imprimir"]');
            if (!a) return;
            const url = a.getAttribute('href');
            if (!url || url === '#') return;
            e.preventDefault();
            e.stopImmediatePropagation();
            imprimirPdf(url, a);
        }, true);

        // Botón "Imprimir" dentro del modal de vista previa
        const btnPrintPrev = document.getElementById('rxPreviewPrint');
        if (btnPrintPrev) {
            btnPrintPrev.addEventListener('click', function (e) {
                e.stopImmediatePropagation();
                const frame = document.getElementById('rxPreviewFrame');
                const src = frame && frame.src && frame.src !== 'about:blank' ? frame.src : null;
                if (src) imprimirPdf(src);
            }, true);
        }
    });

    // ---------- Imprimir historia clínica: mismo PDF de la descarga, en el diálogo de impresión ----------
    modulo('imprimirHistoria', function () {
        document.addEventListener('click', function (e) {
            const b = e.target.closest('.js-imprimir-historia');
            if (!b) return;
            e.preventDefault();
            e.stopImmediatePropagation();

            const url = b.dataset.url || b.getAttribute('href');
            if (!url || url === '#') {
                Swal.fire('Sin consulta', 'Guarda la consulta primero para poder imprimirla.', 'info');
                return;
            }
            if (window.hcImprimirPdf) window.hcImprimirPdf(url, b);
            else window.open(url, '_blank');
        }, true);
    });

    // ---------- Fecha y hora en vivo (solo en consultas nuevas) ----------
    modulo('fecha', function () {
        const t = document.getElementById('hcFechaTitulo');
        const h = document.getElementById('hcFecha');
        if (!t) return;

        const pad = n => String(n).padStart(2, '0');

        function pintar() {
            if (t.dataset.live !== '1') return;      // consulta ya guardada: la fecha queda fija
            const d = new Date();
            const mes = d.toLocaleDateString('es-CO', { month: 'long' });
            t.textContent = d.getDate() + ' de ' + mes.charAt(0).toUpperCase() + mes.slice(1) + ' de ' + d.getFullYear() +
                ' - ' + pad(d.getHours() % 12 || 12) + ':' + pad(d.getMinutes()) + ' ' + (d.getHours() < 12 ? 'AM' : 'PM');
            if (h) {
                h.value = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + ' ' +
                    pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }
        }

        // Expuesta para que reiniciarFormulario() pueda forzar el repintado
        window.hcPintarFecha = pintar;

        pintar();
        setInterval(pintar, 30000);
    });

    // ---------- Vista previa de los archivos recién elegidos ----------
    modulo('adjuntos', function () {
        const input = document.getElementById('hcSubirArchivo');
        const cont = document.getElementById('hcNuevos');
        if (!input || !cont) return;

        input.addEventListener('change', () => {
            const MAX = 10 * 1024 * 1024;   // igual que la validación del servidor
            const grandes = [...input.files].filter(f => f.size > MAX);
            if (grandes.length) {
                Swal.fire('Archivo demasiado grande',
                    'Estos archivos superan los 10 MB: ' + grandes.map(f => f.name).join(', '), 'warning');
                input.value = '';
                cont.innerHTML = '';
                return;
            }

            // Revoca URLs anteriores para no dejar blobs colgando
            cont.querySelectorAll('img').forEach(i => URL.revokeObjectURL(i.src));
            cont.innerHTML = '';

            [...input.files].forEach(f => {
                if (f.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = 'hc-file-img';
                    img.src = URL.createObjectURL(f);
                    img.alt = f.name;
                    cont.appendChild(img);
                } else {
                    const doc = document.createElement('div');
                    doc.className = 'hc-file-doc';
                    doc.innerHTML = '<i class="far fa-file-pdf"></i><div><strong></strong><span></span></div>';
                    doc.querySelector('strong').textContent = f.name;
                    doc.querySelector('span').textContent = Math.round(f.size / 1024) + ' KB';
                    cont.appendChild(doc);
                }
            });
        });
    });

    // ---------- Guardar la consulta sin recargar ----------
    modulo('guardar', function () {
        const form = document.getElementById('formHistoria');
        if (!form) return;

        const token = form.querySelector('input[name=_token]').value;
        const btn = document.getElementById('hcGuardar');

        // Deja el formulario como "consulta nueva": campos, adjuntos, recetas y modo de guardado
        function reiniciarFormulario() {
            ['motivo', 'anamnesis', 'temperatura', 'frecuencia_cardiaca', 'frecuencia_respiratoria',
             'condicion_corporal', 'examen_fisico', 'diagnostico', 'tratamiento', 'observaciones', 'proxima_cita']
                .forEach(n => { if (form.elements[n]) form.elements[n].value = ''; });

            if (form.elements['peso'])        form.elements['peso'].value = form.dataset.pesoDefecto || '';
            if (form.elements['hidratacion']) form.elements['hidratacion'].value = 'Normal';

            // Vuelve al modo "crear" (POST a store, sin _method)
            form.action = form.dataset.urlStore || form.action;
            const metodo = form.querySelector('input[name="_method"]');
            if (metodo) metodo.remove();
            form.querySelectorAll('input[name="recetas_ids[]"]').forEach(i => i.remove());

            // El modal de receta deja de apuntar a la consulta anterior
            const rx = document.querySelector('#formReceta input[name="historia_clinica_id"]');
            if (rx) rx.value = '';

            // Fórmula médica
            hc.limpiarLista('receta', 'Aún no hay recetas para esta mascota.');
            const formulaAnt = document.getElementById('hcFormulaAnterior');
            if (formulaAnt) formulaAnt.remove();

            // Adjuntos: los recién elegidos y los de la consulta anterior
            const input = document.getElementById('hcSubirArchivo');
            if (input) input.value = '';
            const nuevos = document.getElementById('hcNuevos');
            if (nuevos) {
                nuevos.querySelectorAll('img').forEach(i => URL.revokeObjectURL(i.src));
                nuevos.innerHTML = '';
            }
            const existentes = document.getElementById('hcArchivosExistentes');
            if (existentes) existentes.innerHTML = '';

            // Fecha en vivo otra vez, y sin botón "Nueva consulta" (ya no hay una abierta)
            const t = document.getElementById('hcFechaTitulo');
            if (t) {
                t.dataset.live = '1';
                if (window.hcPintarFecha) window.hcPintarFecha();
            }
            document.querySelectorAll('.hc-consulta-actions a[href*="nueva=1"]').forEach(a => a.remove());

            if (form.elements['motivo']) form.elements['motivo'].focus();
        }

        // Trae la página actualizada y cambia solo el Resumen y el historial lateral
        async function refrescarVista(url) {
            try {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const doc = new DOMParser().parseFromString(await res.text(), 'text/html');

                ['[data-panel="resumen"]', '#hcTimeline'].forEach(sel => {
                    const nuevo = doc.querySelector(sel);
                    const actual = document.querySelector(sel);
                    if (nuevo && actual) actual.innerHTML = nuevo.innerHTML;
                });
            } catch (err) { /* si falla el refresco, lo guardado ya está a salvo */ }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const html = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            try {
                const fd = new FormData(form);
                if (!fd.get('mascota_id') && form.dataset.mascota) fd.set('mascota_id', form.dataset.mascota);

                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const json = await res.json().catch(() => ({}));

                if (!res.ok) {
                    console.warn('Consulta:', res.status, json);
                    Swal.fire('Error ' + res.status, hc.errores(json, 'No se pudo guardar la consulta.'), 'error');
                    return;   // los datos escritos se conservan
                }

                // Siempre queda listo para una consulta nueva; la URL deja de apuntar a la guardada
                reiniciarFormulario();

                const u = new URL(json.redirect);
                u.searchParams.delete('consulta');
                u.searchParams.set('nueva', '1');
                history.replaceState(null, '', u.pathname + u.search + location.hash);

                await refrescarVista(u.toString());
                hc.aviso(json.message);
            } catch (err) {
                Swal.fire('Error', 'No se pudo guardar la consulta.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = html;
            }
        });
    });

})();
</script>
@endpush