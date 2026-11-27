@extends('layouts.app')

@section('subtitle', 'Configuración')

@section('content_body')

<br>

<div class="vc-page-header">
    <div class="vc-page-header__left">
        <span class="vc-page-icon"><i class="fas fa-cogs"></i></span>
        <div>
            <h1>Configuración</h1>
            <p>Datos de la clínica, facturación, horario, catálogos y permisos por rol</p>
        </div>
    </div>
</div>

{{-- ===================== PESTAÑAS ===================== --}}
<div class="cf-tabs" id="cfTabs">
    <button type="button" class="cf-tab is-active" data-pane="general"><i class="fas fa-clinic-medical"></i> General</button>
    <button type="button" class="cf-tab" data-pane="facturacion"><i class="fas fa-file-invoice-dollar"></i> Facturación</button>
    <button type="button" class="cf-tab" data-pane="horario"><i class="fas fa-clock"></i> Horario y citas</button>
    <button type="button" class="cf-tab" data-pane="catalogos"><i class="fas fa-list"></i> Catálogos</button>
    <button type="button" class="cf-tab" data-pane="permisos"><i class="fas fa-user-shield"></i> Roles y permisos</button>
</div>

{{-- ===================== GENERAL ===================== --}}
<div class="cf-pane is-active" id="pane-general">
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-clinic-medical"></i> Datos de la clínica</span>
        </div>
        <div class="vc-card__body">
            <form id="formGeneral" enctype="multipart/form-data" autocomplete="off">
                <div class="cf-error alert alert-danger" style="display:none;"></div>

                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="cf-logo">
                            <img id="cfLogoImg" src="{{ $cfg->logo_url }}" alt="Logo"
                                 style="{{ $cfg->logo_url ? '' : 'display:none;' }}">
                            <div class="cf-logo__vacio" id="cfLogoVacio" style="{{ $cfg->logo_url ? 'display:none;' : '' }}">
                                <i class="fas fa-image"></i>
                                <span>Sin logo</span>
                            </div>
                        </div>
                        <label class="btn btn-outline-primary btn-sm mt-2" for="cfLogo">
                            <i class="fas fa-upload"></i> Subir logo
                        </label>
                        <input type="file" name="logo" id="cfLogo" accept="image/*" style="display:none;">
                        <div class="custom-control custom-checkbox mt-2" id="cfQuitarLogoWrap"
                             style="{{ $cfg->logo ? '' : 'display:none;' }}">
                            <input type="checkbox" class="custom-control-input" id="cfQuitarLogo" name="quitar_logo" value="1">
                            <label class="custom-control-label" for="cfQuitarLogo">Quitar logo</label>
                        </div>
                        <small class="text-muted d-block mt-1">JPG, PNG o WEBP · máx. 2 MB</small>
                    </div>

                    <div class="col-md-9">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nombre de la clínica <span class="text-danger">*</span></label>
                                <input type="text" name="nombre_clinica" class="form-control" maxlength="120" required
                                       value="{{ $cfg->nombre_clinica }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Razón social</label>
                                <input type="text" name="razon_social" class="form-control" maxlength="150"
                                       value="{{ $cfg->razon_social }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>NIT</label>
                                <input type="text" name="nit" class="form-control" maxlength="30" placeholder="900.123.456-7"
                                       value="{{ $cfg->nit }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" class="form-control" maxlength="30"
                                       value="{{ $cfg->telefono }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Correo</label>
                                <input type="email" name="email" class="form-control" maxlength="150"
                                       value="{{ $cfg->email }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Dirección</label>
                                <input type="text" name="direccion" class="form-control" maxlength="255"
                                       value="{{ $cfg->direccion }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" maxlength="100"
                                       value="{{ $cfg->ciudad }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Sitio web</label>
                                <input type="text" name="sitio_web" class="form-control" maxlength="150"
                                       value="{{ $cfg->sitio_web }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary cf-guardar"><i class="fas fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== FACTURACIÓN ===================== --}}
<div class="cf-pane" id="pane-facturacion">
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-file-invoice-dollar"></i> Parámetros de facturación</span>
        </div>
        <div class="vc-card__body">
            <form id="formFacturacion" autocomplete="off">
                <div class="cf-error alert alert-danger" style="display:none;"></div>

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>IVA por defecto (%) <span class="text-danger">*</span></label>
                        <input type="number" name="iva_porcentaje" class="form-control" min="0" max="100" step="0.01" required
                               value="{{ rtrim(rtrim(number_format($cfg->iva_porcentaje, 2, '.', ''), '0'), '.') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Prefijo de factura <span class="text-danger">*</span></label>
                        <input type="text" name="prefijo_factura" id="cfPrefijo" class="form-control" maxlength="10" required
                               value="{{ $cfg->prefijo_factura }}">
                        <small class="text-muted">Ejemplo: <strong id="cfPrefijoEjemplo">{{ $cfg->prefijo_factura }}000001</strong></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Moneda</label>
                        <select name="moneda" class="form-control">
                            <option value="COP" {{ $cfg->moneda === 'COP' ? 'selected' : '' }}>COP · Peso colombiano</option>
                            <option value="USD" {{ $cfg->moneda === 'USD' ? 'selected' : '' }}>USD · Dólar</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Formato de impresión por defecto</label>
                        <select name="formato_impresion" class="form-control">
                            <option value="ticket" {{ $cfg->formato_impresion === 'ticket' ? 'selected' : '' }}>Ticket (80 mm)</option>
                            <option value="carta" {{ $cfg->formato_impresion === 'carta' ? 'selected' : '' }}>Factura carta</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mensaje al pie de la factura</label>
                    <textarea name="pie_factura" class="form-control" rows="2" maxlength="500"
                              placeholder="Gracias por su compra...">{{ $cfg->pie_factura }}</textarea>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary cf-guardar"><i class="fas fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== HORARIO ===================== --}}
<div class="cf-pane" id="pane-horario">
    <div class="vc-card">
        <div class="vc-card__header">
            <span class="vc-card__title"><i class="fas fa-clock"></i> Horario de atención y duración de citas</span>
        </div>
        <div class="vc-card__body">
            <form id="formHorario" autocomplete="off">
                <div class="cf-error alert alert-danger" style="display:none;"></div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label>Duración de cada cita (minutos) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_cita_min" class="form-control" min="5" max="240" step="5" required
                               value="{{ $cfg->duracion_cita_min }}">
                    </div>
                </div>

                <div class="cf-scroll">
                    <table class="cf-tabla">
                        <thead>
                            <tr><th>Día</th><th class="cf-c">Atiende</th><th>Apertura</th><th>Cierre</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($dias as $clave => $etiqueta)
                                @php $h = $horario[$clave]; @endphp
                                <tr class="cf-dia {{ $h['abierto'] ? '' : 'is-cerrado' }}">
                                    <td><strong>{{ $etiqueta }}</strong></td>
                                    <td class="cf-c">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input cf-dia-abierto"
                                                   id="cfDia_{{ $clave }}" name="horario[{{ $clave }}][abierto]" value="1"
                                                   {{ $h['abierto'] ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="cfDia_{{ $clave }}"></label>
                                        </div>
                                    </td>
                                    <td><input type="time" class="form-control form-control-sm" name="horario[{{ $clave }}][desde]" value="{{ $h['desde'] }}"></td>
                                    <td><input type="time" class="form-control form-control-sm" name="horario[{{ $clave }}][hasta]" value="{{ $h['hasta'] }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary cf-guardar"><i class="fas fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== CATÁLOGOS ===================== --}}
<div class="cf-pane" id="pane-catalogos">
    <div class="cf-cat">
        <div class="cf-cat__tipos" id="cfCatTipos">
            @foreach ($tipos as $clave => $t)
                <button type="button" class="cf-cat__tipo {{ $loop->first ? 'is-active' : '' }}" data-tipo="{{ $clave }}">
                    <i class="fas {{ $t['icono'] }}"></i> {{ $t['nombre'] }}
                </button>
            @endforeach
        </div>

        <div class="vc-card cf-cat__main">
            <div class="vc-card__header cf-cat__head">
                <span class="vc-card__title"><i class="fas fa-list"></i> <span id="cfCatTitulo">Especies</span></span>
                <span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="cfCatSugeridos" style="display:none;">
                        <i class="fas fa-magic"></i> Cargar sugeridos
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="cfCatNuevo">
                        <i class="fas fa-plus"></i> <span id="cfCatNuevoTxt">Nuevo</span>
                    </button>
                </span>
            </div>
            <div class="vc-card__body" style="padding:0;">
                <div class="cf-scroll">
                    <table class="cf-tabla" id="cfCatTabla">
                        <thead></thead>
                        <tbody><tr><td class="cf-vacio">Cargando...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== ROLES Y PERMISOS ===================== --}}
<div class="cf-pane" id="pane-permisos">
    <div class="vc-card">
        <div class="vc-card__header cf-cat__head">
            <span class="vc-card__title"><i class="fas fa-user-shield"></i> Permisos por módulo</span>
            <div class="cf-roles" id="cfRoles">
                @foreach ($roles as $rol)
                    <button type="button" class="cf-rol {{ $loop->first ? 'is-active' : '' }}" data-rol="{{ $rol }}">{{ $rol }}</button>
                @endforeach
            </div>
        </div>
        <div class="vc-card__body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                El rol <strong>Admin</strong> siempre tiene acceso total y no se puede restringir.
                Los permisos se llaman <code>ver-facturas</code>, <code>crear-ventas</code>, etc.
                Si das crear, editar o eliminar en un módulo, también se activa Ver.
            </div>

            <div class="cf-error alert alert-danger" style="display:none;" id="cfPermError"></div>

            <div class="cf-scroll">
                <table class="cf-tabla" id="cfPermTabla">
                    <thead></thead>
                    <tbody><tr><td class="cf-vacio">Cargando...</td></tr></tbody>
                </table>
            </div>

            <div class="text-right mt-3">
                <button type="button" class="btn btn-primary" id="cfPermGuardar">
                    <i class="fas fa-save"></i> Guardar permisos de <span id="cfPermRolTxt"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<br>

{{-- ===================== MODAL CATÁLOGO ===================== --}}
<div class="modal fade" id="modalCatalogo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content cf-modal">
            <form id="formCatalogo" autocomplete="off">
                <input type="hidden" id="cf_cat_id" value="">
                <div class="modal-header cf-modal__head" id="cfCatHead">
                    <h5 class="modal-title" id="cfCatModalTitulo">Nuevo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="cfCatErrores" style="display:none;"></div>

                    <div class="form-group" id="cfCatPadreWrap" style="display:none;">
                        <label for="cf_cat_padre" id="cfCatPadreLbl">Especie</label>
                        <select id="cf_cat_padre" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="cf_cat_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="cf_cat_nombre" class="form-control" maxlength="100" required>
                    </div>
                    <div class="form-group" id="cfCatValorWrap" style="display:none;">
                        <label for="cf_cat_valor">Valor / precio</label>
                        <input type="number" id="cf_cat_valor" class="form-control" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="cf_cat_desc">Descripción</label>
                        <input type="text" id="cf_cat_desc" class="form-control" maxlength="255">
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="cf_cat_activo" checked>
                        <label class="custom-control-label" for="cf_cat_activo">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="cfCatGuardar"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL ELIMINAR CATÁLOGO ===================== --}}
<div class="modal fade" id="modalEliminarCatalogo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content cf-modal">
            <div class="modal-header cf-modal__head" style="background:#dc3545;">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Eliminar registro</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1" style="font-size:42px;color:#dc3545;"><i class="fas fa-exclamation-circle"></i></p>
                <p class="mb-1">¿Seguro que deseas eliminar</p>
                <p><strong id="cfElimNombre"></strong>?</p>
                <div class="alert alert-danger" id="cfElimError" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="cfElimConfirmar"><i class="fas fa-trash"></i> Sí, eliminar</button>
            </div>
        </div>
    </div>
</div>

@stop

@push('css')
<style>
    .cf-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .cf-tab {
        border: 1px solid #E5EAF2; background: #fff; color: #475569; border-radius: 10px;
        padding: 9px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .cf-tab:hover { border-color: #2F6FED; color: #2F6FED; }
    .cf-tab.is-active { background: #2F6FED; border-color: #2F6FED; color: #fff; box-shadow: 0 4px 12px rgba(47,111,237,.25); }

    .cf-pane { display: none; }
    .cf-pane.is-active { display: block; }

    .cf-logo {
        width: 160px; height: 160px; margin: 0 auto; border: 2px dashed #CBD5E1; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; background: #F8FAFC; overflow: hidden;
    }
    .cf-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .cf-logo__vacio { color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 12px; }
    .cf-logo__vacio i { font-size: 34px; }

    .cf-scroll { overflow-x: auto; }
    .cf-tabla { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cf-tabla th {
        background: #F1F5F9; color: #475569; font-size: 12px; text-align: left; padding: 10px 14px;
        border-bottom: 1px solid #E2E8F0; white-space: nowrap;
    }
    .cf-tabla td { padding: 9px 14px; border-bottom: 1px solid #EEF1F6; color: #1e293b; vertical-align: middle; }
    .cf-tabla tbody tr:hover { background: #F8FAFF; }
    .cf-c { text-align: center !important; }
    .cf-vacio { text-align: center; color: #94a3b8; padding: 28px 12px !important; }
    .cf-dia.is-cerrado td:not(:first-child):not(:nth-child(2)) { opacity: .4; }
    .cf-dia input[type="time"] { max-width: 140px; }

    .cf-cat { display: grid; grid-template-columns: 230px 1fr; gap: 16px; align-items: start; }
    .cf-cat__tipos { display: flex; flex-direction: column; gap: 6px; }
    .cf-cat__tipo {
        text-align: left; border: 1px solid #E5EAF2; background: #fff; color: #475569; border-radius: 10px;
        padding: 10px 14px; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .cf-cat__tipo i { width: 20px; color: #2F6FED; }
    .cf-cat__tipo:hover { border-color: #2F6FED; }
    .cf-cat__tipo.is-active { background: #F3F7FF; border-color: #2F6FED; box-shadow: inset 3px 0 0 #2F6FED; }
    .cf-cat__head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }

    .cf-roles { display: flex; gap: 6px; flex-wrap: wrap; }
    .cf-rol {
        border: 1px solid #D5DCE8; background: #fff; color: #475569; border-radius: 999px;
        padding: 5px 14px; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    .cf-rol:hover { border-color: #2F6FED; color: #2F6FED; }
    .cf-rol.is-active, .cf-rol.is-active:hover { background: #2F6FED !important; border-color: #2F6FED !important; color: #fff !important; }
    /* modo oscuro: no seleccionado en gris oscuro, seleccionado en azul (con prioridad sobre cualquier otro estilo) */
    html[data-theme="dark"] .cf-roles .cf-rol:not(.is-active) { background: #273449 !important; border-color: #334155 !important; color: #94a3b8 !important; }
    html[data-theme="dark"] .cf-roles .cf-rol.is-active,
    html[data-theme="dark"] .cf-roles .cf-rol.is-active:hover { background: #2F6FED !important; border-color: #2F6FED !important; color: #fff !important; }

    .cf-badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .cf-badge.ok { background: #DCFCE7; color: #15803d; }
    .cf-badge.no { background: #FEE2E2; color: #b91c1c; }

    .cf-modal { border: 0; border-radius: 12px; overflow: hidden; }
    .cf-modal__head { color: #fff; border-bottom: 0; }
    .cf-modal__head .modal-title { font-size: 17px; font-weight: 600; }

    @media (max-width: 767px) {
        .cf-cat { grid-template-columns: 1fr; }
        .cf-cat__tipos { flex-direction: row; flex-wrap: wrap; }
    }
</style>
@endpush

@push('js')
@php
    // Se arma aquí porque la directiva json de Blade separa por comas y rompe arreglos con varias claves
    $tiposJs = [];
    foreach ($tipos as $clave => $t) {
        $tiposJs[$clave] = [
            'nombre'    => $t['nombre'],
            'singular'  => $t['singular'],
            'valor'     => $t['valor'],
            'padre'     => $t['padre'],
            'sugeridos' => count($t['sugeridos']) > 0,
        ];
    }

    $cfgJs = [
        'csrf'     => csrf_token(),
        'tipos'    => $tiposJs,
        'modulos'  => $modulos,
        'acciones' => $acciones,
        'roles'    => array_values($roles),
        'urls'     => [
            'general'      => route('configuracion.general'),
            'facturacion'  => route('configuracion.facturacion'),
            'horario'      => route('configuracion.horario'),
            'catIndex'     => route('configuracion.catalogos.index', ['tipo' => '__T__']),
            'catStore'     => route('configuracion.catalogos.store'),
            'catUpdate'    => route('configuracion.catalogos.update', ['id' => '__ID__']),
            'catDestroy'   => route('configuracion.catalogos.destroy', ['id' => '__ID__']),
            'catSugeridos' => route('configuracion.catalogos.sugeridos', ['tipo' => '__T__']),
            'permisos'     => route('configuracion.permisos', ['rol' => '__R__']),
            'permGuardar'  => route('configuracion.permisos.guardar', ['rol' => '__R__']),
        ],
    ];
@endphp
<script>
(function () {
    if (window.__configuracionIniciada) return;
    window.__configuracionIniciada = true;

    function arrancar() {

    const CFG = @json($cfgJs);

    const $  = (s, c) => (c || document).querySelector(s);
    const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    /* ---------------- utilidades ---------------- */
    function aviso(tipo, texto) {
        if (window.Swal) {
            Swal.fire({ icon: tipo, title: texto, timer: 1800, timerProgressBar: true, showConfirmButton: false });
        } else {
            alert(texto);
        }
    }

    async function api(url, opts) {
        opts = opts || {};
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CFG.csrf
        };
        let body = opts.body;
        if (body && !(body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            body = JSON.stringify(body);
        }

        const res = await fetch(url, { method: opts.method || 'GET', headers: headers, body: body, credentials: 'same-origin' });
        const texto = await res.text();
        let json = null;
        try { json = JSON.parse(texto); } catch (e) { /* no era JSON */ }

        if (!res.ok || (json && json.success === false)) {
            const err = new Error((json && json.message) || ('Error ' + res.status));
            err.status = res.status;
            err.json = json;
            err.raw = texto;
            throw err;
        }
        return json || {};
    }

    function mensajesDe(e) {
        if (e.status === 422 && e.json && e.json.errors) {
            return Object.values(e.json.errors).reduce((a, b) => a.concat(b), []);
        }
        if (e.status === 419) return ['La sesión expiró. Recarga la página e inténtalo de nuevo.'];
        if (e.status === 403) return [(e.json && e.json.message) || 'No tienes permiso para esta acción.'];
        if (e.status === undefined) return ['No se pudo conectar con el servidor.'];
        if (e.status >= 500) console.error('[Configuración] HTTP ' + e.status, (e.raw || '').slice(0, 600));
        return [(e.json && e.json.message) || ('Error ' + e.status + ' en el servidor.')];
    }

    function mostrarErrores(caja, mensajes) {
        if (!caja) return;
        if (!mensajes || !mensajes.length) { caja.style.display = 'none'; caja.innerHTML = ''; return; }
        caja.innerHTML = mensajes.map(esc).join('<br>');
        caja.style.display = 'block';
    }

    const modal = (sel, accion) => { if (window.jQuery) window.jQuery(sel).modal(accion); };

    /* ---------------- pestañas ---------------- */
    let catCargado = false, permCargado = false;

    function mostrarPane(nombre) {
        if (!$('#pane-' + nombre)) nombre = 'general';
        $$('.cf-tab').forEach((b) => b.classList.toggle('is-active', b.dataset.pane === nombre));
        $$('.cf-pane').forEach((p) => p.classList.toggle('is-active', p.id === 'pane-' + nombre));
        try { window.history.replaceState(null, '', '#' + nombre); } catch (e) { /* ignorar */ }

        if (nombre === 'catalogos' && !catCargado) { catCargado = true; cargarCatalogo(catTipo); }
        if (nombre === 'permisos' && !permCargado) { permCargado = true; cargarPermisos(rolActual); }
    }

    $('#cfTabs').addEventListener('click', (e) => {
        const b = e.target.closest('.cf-tab');
        if (b) mostrarPane(b.dataset.pane);
    });

    /* ---------------- formularios simples ---------------- */
    async function enviarForm(form, url, opciones) {
        opciones = opciones || {};
        const btn = $('.cf-guardar', form);
        const html = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        mostrarErrores($('.cf-error', form), []);

        try {
            const json = await api(url, { method: 'POST', body: new FormData(form) });
            aviso('success', json.message || 'Guardado correctamente.');
            if (opciones.alExito) opciones.alExito(json);
        } catch (e) {
            mostrarErrores($('.cf-error', form), mensajesDe(e));
        } finally {
            btn.disabled = false;
            btn.innerHTML = html;
        }
    }

    // General (con logo)
    const formGeneral = $('#formGeneral');
    formGeneral.addEventListener('submit', (e) => {
        e.preventDefault();
        enviarForm(formGeneral, CFG.urls.general, {
            alExito: (json) => {
                const img = $('#cfLogoImg');
                if (json.logo_url) {
                    img.src = json.logo_url + '?t=' + Date.now();
                    img.style.display = '';
                    $('#cfLogoVacio').style.display = 'none';
                    $('#cfQuitarLogoWrap').style.display = '';
                } else {
                    img.style.display = 'none';
                    $('#cfLogoVacio').style.display = '';
                    $('#cfQuitarLogoWrap').style.display = 'none';
                }
                $('#cfLogo').value = '';
                $('#cfQuitarLogo').checked = false;
            }
        });
    });

    $('#cfLogo').addEventListener('change', function () {
        const f = this.files && this.files[0];
        if (!f) return;
        const img = $('#cfLogoImg');
        img.src = URL.createObjectURL(f);
        img.style.display = '';
        $('#cfLogoVacio').style.display = 'none';
        $('#cfQuitarLogo').checked = false;
    });

    $('#cfQuitarLogo').addEventListener('change', function () {
        if (this.checked) {
            $('#cfLogoImg').style.display = 'none';
            $('#cfLogoVacio').style.display = '';
            $('#cfLogo').value = '';
        } else if ($('#cfLogoImg').getAttribute('src')) {
            $('#cfLogoImg').style.display = '';
            $('#cfLogoVacio').style.display = 'none';
        }
    });

    // Facturación
    const formFact = $('#formFacturacion');
    formFact.addEventListener('submit', (e) => { e.preventDefault(); enviarForm(formFact, CFG.urls.facturacion); });
    $('#cfPrefijo').addEventListener('input', function () { $('#cfPrefijoEjemplo').textContent = this.value + '000001'; });

    // Horario
    const formHorario = $('#formHorario');
    formHorario.addEventListener('submit', (e) => { e.preventDefault(); enviarForm(formHorario, CFG.urls.horario); });
    formHorario.addEventListener('change', (e) => {
        if (e.target.classList.contains('cf-dia-abierto')) {
            e.target.closest('tr').classList.toggle('is-cerrado', !e.target.checked);
        }
    });

    /* ---------------- catálogos ---------------- */
    let catTipo = 'especie';
    let catItems = [];
    let catPadres = [];
    let itemEliminar = null;

    const metaCat = () => CFG.tipos[catTipo];

    async function cargarCatalogo(tipo) {
        catTipo = tipo;
        const meta = metaCat();

        $$('.cf-cat__tipo').forEach((b) => b.classList.toggle('is-active', b.dataset.tipo === tipo));
        $('#cfCatTitulo').textContent = meta.nombre;
        $('#cfCatNuevoTxt').textContent = 'Nuevo';
        $('#cfCatSugeridos').style.display = meta.sugeridos ? '' : 'none';
        $('#cfCatTabla tbody').innerHTML = '<tr><td class="cf-vacio">Cargando...</td></tr>';

        try {
            const json = await api(CFG.urls.catIndex.replace('__T__', tipo));
            if (tipo !== catTipo) return;                // cambió de pestaña mientras cargaba
            catItems = json.items || [];
            catPadres = json.padres || [];
            renderCatalogo();
        } catch (e) {
            $('#cfCatTabla tbody').innerHTML = '<tr><td class="cf-vacio">No se pudo cargar: ' + esc(mensajesDe(e).join(' ')) + '</td></tr>';
        }
    }

    function renderCatalogo() {
        const meta = metaCat();
        const cols = [];
        if (meta.padre) cols.push('<th>' + esc(CFG.tipos[meta.padre].singular) + '</th>');
        cols.push('<th>Nombre</th>');
        if (meta.valor) cols.push('<th class="cf-c">Valor</th>');
        cols.push('<th>Descripción</th><th class="cf-c">Estado</th><th class="cf-c" style="width:110px;">Acciones</th>');
        $('#cfCatTabla thead').innerHTML = '<tr>' + cols.join('') + '</tr>';

        const ncols = cols.length;

        if (meta.padre && !catPadres.length) {
            $('#cfCatTabla tbody').innerHTML = '<tr><td colspan="' + ncols + '" class="cf-vacio">Primero crea al menos una ' +
                esc(CFG.tipos[meta.padre].singular.toLowerCase()) + ' para poder agregar ' + esc(meta.nombre.toLowerCase()) + '.</td></tr>';
            return;
        }

        $('#cfCatTabla tbody').innerHTML = catItems.length
            ? catItems.map((c) => {
                let fila = '<tr>';
                if (meta.padre) fila += '<td>' + esc(c.padre || '—') + '</td>';
                fila += '<td><strong>' + esc(c.nombre) + '</strong></td>';
                if (meta.valor) {
                    fila += '<td class="cf-c">' + (c.valor !== null && c.valor !== undefined
                        ? '$ ' + Number(c.valor).toLocaleString('es-CO', { maximumFractionDigits: 0 }) : '—') + '</td>';
                }
                fila += '<td>' + esc(c.descripcion || '—') + '</td>';
                fila += '<td class="cf-c"><span class="cf-badge ' + (c.activo ? 'ok' : 'no') + '">' + (c.activo ? 'Activo' : 'Inactivo') + '</span></td>';
                fila += '<td class="cf-c">' +
                    '<button type="button" class="btn btn-sm btn-warning cf-cat-editar" data-id="' + c.id + '" title="Editar"><i class="fas fa-edit"></i></button> ' +
                    '<button type="button" class="btn btn-sm btn-danger cf-cat-eliminar" data-id="' + c.id + '" title="Eliminar"><i class="fas fa-trash"></i></button>' +
                    '</td></tr>';
                return fila;
            }).join('')
            : '<tr><td colspan="' + ncols + '" class="cf-vacio">Aún no hay registros. Usa "Nuevo"' +
                (meta.sugeridos ? ' o "Cargar sugeridos"' : '') + '.</td></tr>';
    }

    $('#cfCatTipos').addEventListener('click', (e) => {
        const b = e.target.closest('.cf-cat__tipo');
        if (b) cargarCatalogo(b.dataset.tipo);
    });

    function abrirModalCat(item) {
        const meta = metaCat();
        const editando = !!item;

        if (meta.padre && !catPadres.length) {
            aviso('info', 'Primero crea una ' + CFG.tipos[meta.padre].singular.toLowerCase() + '.');
            return;
        }

        $('#cf_cat_id').value = editando ? item.id : '';
        $('#cfCatHead').style.background = editando ? '#f59e0b' : '#28a745';
        $('#cfCatModalTitulo').textContent = (editando ? 'Editar ' : 'Nuevo ') + meta.singular.toLowerCase();

        const btn = $('#cfCatGuardar');
        btn.className = 'btn ' + (editando ? 'btn-warning' : 'btn-success');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> ' + (editando ? 'Actualizar' : 'Guardar');

        $('#cfCatPadreWrap').style.display = meta.padre ? '' : 'none';
        if (meta.padre) {
            $('#cfCatPadreLbl').textContent = CFG.tipos[meta.padre].singular;
            $('#cf_cat_padre').innerHTML = catPadres.map((p) =>
                '<option value="' + p.id + '">' + esc(p.nombre) + '</option>').join('');
            if (editando && item.padre_id) $('#cf_cat_padre').value = item.padre_id;
        }
        $('#cfCatValorWrap').style.display = meta.valor ? '' : 'none';

        $('#cf_cat_nombre').value = editando ? item.nombre : '';
        $('#cf_cat_valor').value = editando && item.valor !== null && item.valor !== undefined ? item.valor : '';
        $('#cf_cat_desc').value = editando ? (item.descripcion || '') : '';
        $('#cf_cat_activo').checked = editando ? !!item.activo : true;
        mostrarErrores($('#cfCatErrores'), []);

        modal('#modalCatalogo', 'show');
    }

    $('#cfCatNuevo').addEventListener('click', () => abrirModalCat(null));

    $('#cfCatSugeridos').addEventListener('click', async function () {
        this.disabled = true;
        try {
            const json = await api(CFG.urls.catSugeridos.replace('__T__', catTipo), { method: 'POST' });
            aviso('success', json.message);
            cargarCatalogo(catTipo);
        } catch (e) {
            aviso('error', mensajesDe(e).join(' '));
        } finally {
            this.disabled = false;
        }
    });

    $('#cfCatTabla').addEventListener('click', (e) => {
        const ed = e.target.closest('.cf-cat-editar');
        const el = e.target.closest('.cf-cat-eliminar');
        if (ed) {
            abrirModalCat(catItems.find((c) => String(c.id) === ed.dataset.id));
        } else if (el) {
            itemEliminar = catItems.find((c) => String(c.id) === el.dataset.id);
            if (!itemEliminar) return;
            $('#cfElimNombre').textContent = itemEliminar.nombre;
            $('#cfElimError').style.display = 'none';
            const b = $('#cfElimConfirmar');
            b.disabled = false;
            b.innerHTML = '<i class="fas fa-trash"></i> Sí, eliminar';
            modal('#modalEliminarCatalogo', 'show');
        }
    });

    $('#formCatalogo').addEventListener('submit', async (e) => {
        e.preventDefault();
        const meta = metaCat();
        const id = $('#cf_cat_id').value;
        const btn = $('#cfCatGuardar');
        const html = btn.innerHTML;

        const datos = {
            tipo: catTipo,
            nombre: $('#cf_cat_nombre').value,
            descripcion: $('#cf_cat_desc').value,
            activo: $('#cf_cat_activo').checked
        };
        if (meta.valor) datos.valor = $('#cf_cat_valor').value === '' ? null : $('#cf_cat_valor').value;
        if (meta.padre) datos.padre_id = $('#cf_cat_padre').value;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        mostrarErrores($('#cfCatErrores'), []);

        try {
            const json = id
                ? await api(CFG.urls.catUpdate.replace('__ID__', id), { method: 'PUT', body: datos })
                : await api(CFG.urls.catStore, { method: 'POST', body: datos });
            modal('#modalCatalogo', 'hide');
            await cargarCatalogo(catTipo);
            aviso('success', json.message);
        } catch (err) {
            mostrarErrores($('#cfCatErrores'), mensajesDe(err));
        } finally {
            btn.disabled = false;
            btn.innerHTML = html;
        }
    });

    $('#cfElimConfirmar').addEventListener('click', async function () {
        if (!itemEliminar) return;
        const b = this;
        b.disabled = true;
        b.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
        try {
            const json = await api(CFG.urls.catDestroy.replace('__ID__', itemEliminar.id), { method: 'DELETE' });
            modal('#modalEliminarCatalogo', 'hide');
            await cargarCatalogo(catTipo);
            aviso('success', json.message);
        } catch (err) {
            const el = $('#cfElimError');
            el.textContent = mensajesDe(err).join(' ');
            el.style.display = 'block';
            b.disabled = false;
            b.innerHTML = '<i class="fas fa-trash"></i> Sí, eliminar';
        }
    });

    /* ---------------- roles y permisos ---------------- */
    let rolActual = CFG.roles[0] || '';

    async function cargarPermisos(rol) {
        rolActual = rol;
        $$('.cf-rol').forEach((b) => b.classList.toggle('is-active', b.dataset.rol === rol));
        $('#cfPermRolTxt').textContent = rol;
        mostrarErrores($('#cfPermError'), []);
        $('#cfPermTabla tbody').innerHTML = '<tr><td class="cf-vacio">Cargando...</td></tr>';

        try {
            const json = await api(CFG.urls.permisos.replace('__R__', encodeURIComponent(rol)));
            if (rol !== rolActual) return;
            renderPermisos(json.permisos || []);
        } catch (e) {
            $('#cfPermTabla tbody').innerHTML = '<tr><td class="cf-vacio">No se pudo cargar: ' + esc(mensajesDe(e).join(' ')) + '</td></tr>';
        }
    }

    function renderPermisos(asignados) {
        const set = new Set(asignados);
        const acciones = Object.keys(CFG.acciones);

        $('#cfPermTabla thead').innerHTML = '<tr><th>Módulo</th>' +
            acciones.map((a) => '<th class="cf-c"><label class="mb-0" style="cursor:pointer;">' +
                '<input type="checkbox" class="cf-col" data-acc="' + a + '"> ' + esc(CFG.acciones[a]) + '</label></th>').join('') +
            '<th class="cf-c">Todo</th></tr>';

        $('#cfPermTabla tbody').innerHTML = Object.keys(CFG.modulos).map((m) =>
            '<tr data-mod="' + m + '"><td><strong>' + esc(CFG.modulos[m]) + '</strong></td>' +
            acciones.map((a) => '<td class="cf-c"><input type="checkbox" class="cf-perm" data-acc="' + a + '" data-mod="' + m + '"' +
                (set.has(a + '-' + m) ? ' checked' : '') + '></td>').join('') +
            '<td class="cf-c"><input type="checkbox" class="cf-fila" data-mod="' + m + '"></td></tr>'
        ).join('');

        actualizarMarcas();
    }

    function actualizarMarcas() {
        const acciones = Object.keys(CFG.acciones);
        Object.keys(CFG.modulos).forEach((m) => {
            const todos = acciones.every((a) => $('.cf-perm[data-acc="' + a + '"][data-mod="' + m + '"]').checked);
            $('.cf-fila[data-mod="' + m + '"]').checked = todos;
        });
        acciones.forEach((a) => {
            const todos = Object.keys(CFG.modulos).every((m) => $('.cf-perm[data-acc="' + a + '"][data-mod="' + m + '"]').checked);
            $('.cf-col[data-acc="' + a + '"]').checked = todos;
        });
    }

    $('#cfPermTabla').addEventListener('change', (e) => {
        const t = e.target;

        if (t.classList.contains('cf-col')) {
            $$('.cf-perm[data-acc="' + t.dataset.acc + '"]').forEach((c) => { c.checked = t.checked; });
            if (t.checked && t.dataset.acc !== 'ver') $$('.cf-perm[data-acc="ver"]').forEach((c) => { c.checked = true; });
            if (!t.checked && t.dataset.acc === 'ver') $$('.cf-perm').forEach((c) => { c.checked = false; });
        } else if (t.classList.contains('cf-fila')) {
            $$('.cf-perm[data-mod="' + t.dataset.mod + '"]').forEach((c) => { c.checked = t.checked; });
        } else if (t.classList.contains('cf-perm')) {
            if (t.checked && t.dataset.acc !== 'ver') {
                $('.cf-perm[data-acc="ver"][data-mod="' + t.dataset.mod + '"]').checked = true;
            }
            if (!t.checked && t.dataset.acc === 'ver') {
                $$('.cf-perm[data-mod="' + t.dataset.mod + '"]').forEach((c) => { c.checked = false; });
            }
        }
        actualizarMarcas();
    });

    $('#cfRoles').addEventListener('click', (e) => {
        const b = e.target.closest('.cf-rol');
        if (b && b.dataset.rol !== rolActual) cargarPermisos(b.dataset.rol);
    });

    $('#cfPermGuardar').addEventListener('click', async function () {
        const btn = this;
        const html = btn.innerHTML;
        const permisos = $$('.cf-perm:checked').map((c) => c.dataset.acc + '-' + c.dataset.mod);

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        mostrarErrores($('#cfPermError'), []);

        try {
            const json = await api(CFG.urls.permGuardar.replace('__R__', encodeURIComponent(rolActual)), {
                method: 'POST', body: { permisos: permisos }
            });
            renderPermisos(json.permisos || permisos);
            aviso('success', json.message);
        } catch (e) {
            mostrarErrores($('#cfPermError'), mensajesDe(e));
        } finally {
            btn.disabled = false;
            btn.innerHTML = html;
        }
    });

    /* ---------------- inicio ---------------- */
    mostrarPane((window.location.hash || '').replace('#', '') || 'general');
    }

    // El layout puede imprimir el script antes de que exista el HTML
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', arrancar);
    else arrancar();
})();
</script>
@endpush