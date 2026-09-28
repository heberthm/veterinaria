@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-users text-primary"></i> Clientes
        </h1>
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>
    </div>
@stop

@section('content')
    <div class="container-fluid">

        <div class="card card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Listado de Clientes
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btnNuevoCliente">
                        <i class="fas fa-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>

            <div class="card-body">
             <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover table-striped" style="width:100%; font-size:12.5px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Celular</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Mascotas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
              </div>  
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 MODAL: CREAR / EDITAR CLIENTE                             --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:14px;border:none;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tituloModalCliente">
                        <i class="fas fa-user-plus"></i> Nuevo Cliente
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formCliente" autocomplete="off">
                        <input type="hidden" name="id" id="clienteId">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo Documento <span class="text-danger">*</span></label>
                                    <select name="tipo_documento" id="tipo_documento" class="form-control form-control-sm" required>
                                        <option value="CC">CC - Cédula</option>
                                        <option value="CE">CE - Cédula Extranjería</option>
                                        <option value="NIT">NIT</option>
                                        <option value="PA">PA - Pasaporte</option>
                                        <option value="RC">RC - Registro Civil</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Número Documento <span class="text-danger">*</span></label>
                                    <input type="text" name="numero_documento" id="numero_documento"
                                           class="form-control form-control-sm" required maxlength="20"
                                           oninput="this.value = this.value.replace(/[^0-9A-Za-z-]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Celular</label>
                                    <input type="text" name="celular" id="celular"
                                           class="form-control form-control-sm" maxlength="20">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombres <span class="text-danger">*</span></label>
                                    <input type="text" name="nombres" id="nombres"
                                           class="form-control form-control-sm" required maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" name="apellidos" id="apellidos"
                                           class="form-control form-control-sm" required maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ciudad</label>
                                    <input type="text" name="ciudad" id="ciudad"
                                           class="form-control form-control-sm" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Barrio</label>
                                    <input type="text" name="barrio" id="barrio"
                                           class="form-control form-control-sm" maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Nacimiento</label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                           class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Género</label>
                                    <select name="genero" id="genero" class="form-control form-control-sm">
                                        <option value="">Seleccionar...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" checked>
                            <label class="custom-control-label" for="activo">Cliente activo</label>
                        </div>

                        <div id="erroresCliente" class="alert alert-danger mt-3 mb-0" style="display:none;"></div>
                    </form>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnGuardarCliente">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 MODAL: VER DETALLE CLIENTE                                --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalDetalleCliente" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:14px;border:none;">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalle del Cliente
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detalleClienteBody">
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    #tablaClientes thead th {
        background: #f6f7f8;
        color: rgb(14, 1, 1)f0f;
        font-size: 12.5px;
        white-space: nowrap;
    }
    #tablaClientes tbody td {
        font-size: 13px;
        vertical-align: middle;
    }
    .badge-activo   { background:#28a745; color:#fff; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; }
    .badge-inactivo { background:#dc3545; color:#fff; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; }
   .btn-accion {
    display:inline-flex; align-items:center; justify-content:center;
    width:24px; height:24px; border-radius:5px; border:none;
    color:#fff; font-size:10px; cursor:pointer; margin:0 1px;
    padding:0; line-height:1;
    transition: transform .1s, filter .15s;
    }
    .btn-accion:hover  { filter: brightness(1.1); }
    .btn-accion:active { transform: scale(.95); }
    .btn-accion i      { font-size:10px; }

    .btn-accion-ver      { background:#007bff; }
    .btn-accion-editar   { background:#ffc107; color:#212529; }
    .btn-accion-eliminar { background:#dc3545; }
    .cd-item {
        display:flex; align-items:center; gap:10px;
        background:#f8f9fa; padding:10px 14px; border-radius:8px;
        font-size:13px; color:#495057; margin-bottom:6px;
    }
    .cd-item i { color:#007bff; width:16px; }
    .cd-pet {
        display:flex; align-items:center; gap:10px;
        background:#ede7f6; padding:10px 14px; border-radius:8px;
        font-size:13px; color:#5e35b1; margin-bottom:6px;
    }
    .cd-avatar {
        width:56px; height:56px; border-radius:50%;
        background:linear-gradient(135deg,#007bff,#6f42c1);
        color:#fff; font-weight:700; font-size:18px;
        display:flex; align-items:center; justify-content:center;
    }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';

    // ============================================================
    // 🔥 CONFIG
    // ============================================================
    const cfg = {
        rutaDatatable: "{{ route('clientes.datatable') }}",
        rutaStore:     "{{ route('clientes.store') }}",
        rutaBase:      "{{ url('clientes') }}",
        csrfToken:     "{{ csrf_token() }}",
    };

    let tabla = null;

    // ============================================================
    // 🔥 UTILIDADES
    // ============================================================
    const $id  = (id) => document.getElementById(id);
    const val  = (id) => ($id(id)?.value ?? '').trim();
    const setV = (id, v) => { if ($id(id)) $id(id).value = v ?? ''; };
    const setC = (id, v) => { if ($id(id)) $id(id).checked = !!v; };

    function mostrarErrores(html) {
        const box = $id('erroresCliente');
        if (!box) return;
        if (!html) {
            box.style.display = 'none';
            box.innerHTML = '';
            return;
        }
        box.style.display = 'block';
        box.innerHTML = html;
    }

    // ============================================================
    // 🔥 DATATABLE
    // ============================================================
    function inicializarTabla() {
        if (typeof jQuery === 'undefined') {
            console.error('jQuery no cargado');
            return;
        }
        if (typeof jQuery.fn.DataTable === 'undefined') {
            console.error('DataTables no cargado. Activa el plugin en config/adminlte.php');
            return;
        }

        tabla = jQuery('#tablaClientes').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: cfg.rutaDatatable,
                type: 'GET',
                error: function (xhr) {
                    console.error('Error DataTable:', xhr.responseText);
                },
            },
            columns: [
                { data: 'id',             name: 'id',             width: '50px' },
                { data: 'documento',      name: 'numero_documento' },
                { data: 'nombres',        name: 'nombres' },
                { data: 'apellidos',      name: 'apellidos' },
                { data: 'celular',        name: 'celular',  defaultContent: '—' },
                { data: 'email',          name: 'email',    defaultContent: '—' },
                { data: 'ciudad',         name: 'ciudad',   defaultContent: '—' },
                { data: 'mascotas_count', name: 'mascotas_count', searchable: false, orderable: false },
                { data: 'estado',         name: 'activo',         searchable: false },
                { data: 'acciones',       name: 'acciones',       searchable: false, orderable: false },
            ],
            order: [[0, 'desc']],
            pageLength: 15,
            language: {
                processing:     'Procesando...',
                search:         'Buscar:',
                lengthMenu:     'Mostrar _MENU_ registros',
                info:           'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty:      'Mostrando 0 a 0 de 0 registros',
                infoFiltered:   '(filtrado de _MAX_ registros totales)',
                loadingRecords: 'Cargando...',
                zeroRecords:    'No se encontraron resultados',
                emptyTable:     'No hay datos disponibles',
                paginate: {
                    first:    'Primero',
                    previous: 'Anterior',
                    next:     'Siguiente',
                    last:     'Último',
                },
            },
        });
    }

    // ============================================================
    // 🔥 ABRIR MODAL NUEVO
    // ============================================================
    function abrirModalNuevo() {
        const form = $id('formCliente');
        if (form) form.reset();

        setV('clienteId', '');
        setC('activo', true);
        mostrarErrores('');

        const titulo = $id('tituloModalCliente');
        if (titulo) titulo.innerHTML = '<i class="fas fa-user-plus"></i> Nuevo Cliente';

        jQuery('#modalCliente').modal('show');
    }

    // ============================================================
    // 🔥 GUARDAR (crear o editar)
    // ============================================================
    function guardarCliente() {
        const form = $id('formCliente');
        mostrarErrores('');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const id = val('clienteId');
        const esEdicion = !!id;

        const payload = {
            tipo_documento:   val('tipo_documento'),
            numero_documento: val('numero_documento'),
            nombres:          val('nombres'),
            apellidos:        val('apellidos'),
            email:            val('email')     || null,
            celular:          val('celular')   || null,
            direccion:        val('direccion') || null,
            ciudad:           val('ciudad')    || null,
            barrio:           val('barrio')    || null,
            fecha_nacimiento: val('fecha_nacimiento') || null,
            genero:           val('genero')    || null,
            activo:           $id('activo')?.checked ? 1 : 0,
        };

        const url    = esEdicion ? `${cfg.rutaBase}/${id}` : cfg.rutaStore;
        const method = esEdicion ? 'PUT' : 'POST';

        const btn = $id('btnGuardarCliente');
        const btnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  cfg.csrfToken,
                'Accept':        'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(async (res) => {
            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                let msg = data.message || 'Error al guardar el cliente.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(msg);
            }
            return data;
        })
        .then(() => {
            jQuery('#modalCliente').modal('hide');
            if (tabla) tabla.ajax.reload(null, false);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: esEdicion ? 'Cliente actualizado' : 'Cliente creado',
                    timer: 1500,
                    showConfirmButton: false,
                });
            } else {
                alert(esEdicion ? 'Cliente actualizado' : 'Cliente creado');
            }
        })
        .catch((err) => {
            mostrarErrores(err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = btnHtml;
        });
    }

    // ============================================================
    // 🔥 EDITAR
    // ============================================================
    function editarCliente(id) {
        fetch(`${cfg.rutaBase}/${id}/edit`, {
            headers: { 'Accept': 'application/json' },
        })
        .then((res) => {
            if (!res.ok) throw new Error('No se pudo cargar el cliente.');
            return res.json();
        })
        .then((data) => {
            const c = data.cliente || data;

            setV('clienteId',        c.id);
            setV('tipo_documento',   c.tipo_documento || 'CC');
            setV('numero_documento', c.numero_documento || '');
            setV('nombres',          c.nombres || '');
            setV('apellidos',        c.apellidos || '');
            setV('email',            c.email || '');
            setV('celular',          c.celular || '');
            setV('direccion',        c.direccion || '');
            setV('ciudad',           c.ciudad || '');
            setV('barrio',           c.barrio || '');
            setV('fecha_nacimiento', c.fecha_nacimiento || '');
            setV('genero',           c.genero || '');
            setC('activo',           c.activo);

            mostrarErrores('');

            const titulo = $id('tituloModalCliente');
            if (titulo) titulo.innerHTML = '<i class="fas fa-user-edit"></i> Editar Cliente';

            jQuery('#modalCliente').modal('show');
        })
        .catch((err) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            } else {
                alert(err.message);
            }
        });
    }

    // ============================================================
    // 🔥 VER DETALLE
    // ============================================================
    function verCliente(id) {
        const cont = $id('detalleClienteBody');
        cont.innerHTML = '';  // Limpiar por si acaso

        // 👇 El modal NO se abre aquí todavía

        fetch(`${cfg.rutaBase}/${id}/detalle`, {
            headers: { 'Accept': 'application/json' },
        })
        .then((res) => {
            if (!res.ok) throw new Error('No se pudo cargar el detalle.');
            return res.json();
        })
        .then((data) => {
            const c = data.cliente || data;
            const mascotas = (c.mascotas || []).map((m) => `
                <div class="cd-pet">
                    <i class="fas fa-paw"></i>
                    <strong>${escapeHtml(m.nombre || '—')}</strong>
                    <span>${escapeHtml(m.especie || '')} ${escapeHtml(m.raza || '')}</span>
                </div>
            `).join('') || '<p class="text-muted small">Sin mascotas registradas.</p>';

            const inicial = ((c.nombres || '?').charAt(0) + (c.apellidos || '').charAt(0)).toUpperCase();

            cont.innerHTML = `
                <div class="d-flex align-items-center mb-4">
                    <div class="cd-avatar mr-3">${escapeHtml(inicial)}</div>
                    <div>
                        <h5 class="mb-0 font-weight-bold">${escapeHtml(c.nombres || '')} ${escapeHtml(c.apellidos || '')}</h5>
                        <span class="text-muted small">${escapeHtml(c.tipo_documento || '')} ${escapeHtml(c.numero_documento || '')}</span>
                        <span class="ml-2 ${c.activo ? 'badge-activo' : 'badge-inactivo'}">
                            ${c.activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold"><i class="fas fa-address-card"></i> Contacto</h6>
                        ${c.celular   ? `<div class="cd-item"><i class="fas fa-phone"></i> <strong>Celular:</strong> ${escapeHtml(c.celular)}</div>` : ''}
                        ${c.email     ? `<div class="cd-item"><i class="fas fa-envelope"></i> <strong>Email:</strong> ${escapeHtml(c.email)}</div>` : ''}
                        ${c.direccion ? `<div class="cd-item"><i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> ${escapeHtml(c.direccion)}</div>` : ''}
                        ${c.barrio    ? `<div class="cd-item"><i class="fas fa-home"></i> <strong>Barrio:</strong> ${escapeHtml(c.barrio)}</div>` : ''}
                        ${c.ciudad    ? `<div class="cd-item"><i class="fas fa-city"></i> <strong>Ciudad:</strong> ${escapeHtml(c.ciudad)}</div>` : ''}
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Información</h6>
                        ${c.genero           ? `<div class="cd-item"><i class="fas fa-venus-mars"></i> <strong>Género:</strong> ${escapeHtml(c.genero)}</div>` : ''}
                        ${c.fecha_nacimiento ? `<div class="cd-item"><i class="fas fa-birthday-cake"></i> <strong>Nacimiento:</strong> ${escapeHtml(c.fecha_nacimiento)}</div>` : ''}
                        <div class="cd-item">
                            <i class="fas fa-calendar-plus"></i>
                            <strong>Registro:</strong>
                            ${c.created_at ? new Date(c.created_at).toLocaleDateString('es-CO') : '—'}
                        </div>
                    </div>
                </div>

                <h6 class="font-weight-bold mt-3"><i class="fas fa-paw"></i> Mascotas (${(c.mascotas || []).length})</h6>
                ${mascotas}
            `;

            // 👇 AHORA SÍ abrimos el modal, cuando ya hay contenido
            jQuery('#modalDetalleCliente').modal('show');
        })
        .catch((err) => {
            cont.innerHTML = `<div class="alert alert-danger mb-0">${escapeHtml(err.message)}</div>`;
            // Opcional: abrir el modal para mostrar el error
            jQuery('#modalDetalleCliente').modal('show');
        });
    }

    // ============================================================
    // 🔥 ELIMINAR
    // ============================================================
    function eliminarCliente(id) {
        const confirmar = () => {
            return fetch(`${cfg.rutaBase}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrfToken,
                    'Accept':       'application/json',
                },
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Error al eliminar.');
                }
                return data;
            })
            .then(() => {
                if (tabla) tabla.ajax.reload(null, false);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Cliente eliminado', timer: 1500, showConfirmButton: false });
                }
            })
            .catch((err) => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                } else {
                    alert(err.message);
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: '¿Eliminar cliente?',
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) confirmar();
            });
        } else if (confirm('¿Eliminar este cliente?')) {
            confirmar();
        }
    }

    // ============================================================
    // 🔥 ESCAPAR HTML
    // ============================================================
    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g,  '&amp;')
            .replace(/</g,  '&lt;')
            .replace(/>/g,  '&gt;')
            .replace(/"/g,  '&quot;')
            .replace(/'/g,  '&#039;');
    }

    // ============================================================
    // 🔥 EXPONER FUNCIONES GLOBALES (para los onclick de la tabla)
    // ============================================================
    window.editarCliente   = editarCliente;
    window.verCliente      = verCliente;
    window.eliminarCliente = eliminarCliente;

    // ============================================================
    // 🔥 INIT
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        inicializarTabla();

        $id('btnNuevoCliente')?.addEventListener('click', abrirModalNuevo);
        $id('btnGuardarCliente')?.addEventListener('click', guardarCliente);

        // Enter en el form → guardar
        $id('formCliente')?.addEventListener('submit', function (e) {
            e.preventDefault();
            guardarCliente();
        });

        // Limpiar errores al cerrar modal
        jQuery('#modalCliente').on('hidden.bs.modal', function () {
            mostrarErrores('');
        });
    });
})();
</script>
@endpush