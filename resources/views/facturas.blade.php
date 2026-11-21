@extends('layouts.app')

@section('titulo', 'Facturas - VetFlow')
@section('title', 'Facturas')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-file-invoice mr-2"></i>Facturas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Facturas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>Consulta de facturas
                </h3>
            </div>
            <div class="card-body">

                {{-- Filtros --}}
                <div class="row mb-3">
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small text-muted mb-1">Desde</label>
                        <input type="date" id="f-desde" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small text-muted mb-1">Hasta</label>
                        <input type="date" id="f-hasta" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small text-muted mb-1">Estado</label>
                        <select id="f-estado" class="form-control form-control-sm">
                            <option value="">Todos</option>
                            <option value="pagada">Pagada</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="anulada">Anulada</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn-limpiar-filtros">
                            <i class="fas fa-eraser"></i> Limpiar filtros
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla-facturas" class="table table-hover table-striped" style="width:100%; font-size:12.5px;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Pago</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL DETALLE DE FACTURA                                  --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-detalle-factura" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle de Factura
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="detalleFacturaBody" style="padding: 25px; background-color: #ffffff;"></div>

            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="imprimirFactura()" style="background-color: #f59e0b; border: none; border-radius: 8px; padding: 8px 20px; font-weight: 600; color: white;">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL PARA VER EL PDF DE LA FACTURA                       --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPdfFactura" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 90%;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-file-pdf mr-2"></i> Vista Previa de Factura
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; background-color: #525659; height: 75vh; overflow: hidden;">
                <iframe id="pdfViewer" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-print-iframe" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
(function () {
    'use strict';

    if (window.__facturasIniciado) return;
    window.__facturasIniciado = true;

    const BASE = "{{ url('/facturas') }}";

    const esc = (v) => String(v == null ? '' : v)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const dinero = (n) => '$ ' + (parseFloat(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

    function aviso(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: msg, timer: 3500, timerProgressBar: true, showConfirmButton: false });
        } else { alert(msg); }
    }

    function esperar(cb, n) {
        n = n || 0;
        if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) return cb(window.jQuery);
        if (n > 200) return console.error('[Facturas] jQuery/DataTables no están disponibles');
        setTimeout(function () { esperar(cb, n + 1); }, 50);
    }

    // ============================================================
    // VER (detalle) — se abre cuando los datos ya están cargados
    // ============================================================
    window.verFactura = function (id) {
        const $ = window.jQuery;
        if (!id) return;

        $.ajax({
            url: BASE + '/' + id + '/detalle',
            method: 'GET',
            dataType: 'json',
            headers: { 'Accept': 'application/json' },
            success: function (data) {
                const f = (data && (data.factura || data.data)) || data;
                if (!f || !f.id) return aviso('No se pudo cargar la factura.');

                const cli = f.cliente
                    ? [f.cliente.nombres, f.cliente.apellidos].filter(Boolean).join(' ')
                    : 'Consumidor Final';
                const estado = String(f.estado || '').toLowerCase();
                const colores = { pagada: 'success', pendiente: 'warning', anulada: 'danger' };

                const fila = (icono, etiqueta, valor) => `
                    <div class="d-flex align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                        <div class="mr-3 text-center" style="width:25px;color:#3b82f6;"><i class="${icono}"></i></div>
                        <div style="font-size:13px;"><strong>${etiqueta}:</strong> ${valor}</div>
                    </div>`;

                const lineas = f.detalles || [];
                const filas = lineas.length ? lineas.map(function (l) {
                    return `<tr>
                        <td>${esc(l.descripcion)}</td>
                        <td class="text-center">${esc(l.cantidad)}</td>
                        <td class="text-right">${dinero(l.precio_unitario)}</td>
                        <td class="text-right">${dinero(l.subtotal)}</td>
                    </tr>`;
                }).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin productos</td></tr>';

                $('#modal-detalle-factura').data('factura-id', id);
                $('#detalleFacturaBody').html(`
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3"
                             style="width:65px;height:65px;background-color:#3b82f6;font-size:24px;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 font-weight-bold" style="font-size:22px;">Factura ${esc(f.numero_factura)}</h4>
                            <div class="d-flex align-items-center mt-1">
                                <span class="text-muted small mr-2">${esc(cli)}</span>
                                ${estado ? `<span class="badge badge-${colores[estado] || 'secondary'}" style="padding:4px 10px;border-radius:20px;font-size:11px;">${esc(estado.toUpperCase())}</span>` : ''}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-info-circle"></i> Información General</h6>
                            ${fila('fas fa-calendar', 'Fecha', esc(f.fecha_emision || '—'))}
                            ${fila('fas fa-user', 'Cliente', esc(cli))}
                            ${f.cliente && f.cliente.numero_documento ? fila('fas fa-id-card', 'Cédula', esc(f.cliente.numero_documento)) : ''}
                            ${fila('fas fa-user-tie', 'Vendedor', esc(f.vendedor || '—'))}
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-dollar-sign"></i> Totales</h6>
                            ${fila('fas fa-credit-card', 'Método de pago', esc(f.metodo_pago || '—'))}
                            ${fila('fas fa-calculator', 'Subtotal', dinero(f.subtotal))}
                            ${Number(f.descuento) ? fila('fas fa-tag', 'Descuento', dinero(f.descuento)) : ''}
                            ${fila('fas fa-percent', 'IVA', dinero(f.iva))}
                            ${fila('fas fa-coins', 'Total', '<strong>' + dinero(f.total) + '</strong>')}
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2" style="font-weight:700;font-size:15px;"><i class="fas fa-list"></i> Productos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size:13px;">
                            <thead class="thead-light"><tr>
                                <th>Descripción</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Vr. Unit.</th>
                                <th class="text-right">Subtotal</th>
                            </tr></thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                    ${f.observaciones ? `<div class="text-muted small mt-2"><strong>Observaciones:</strong> ${esc(f.observaciones)}</div>` : ''}`);

                $('#modal-detalle-factura').modal('show');
            },
            error: function (xhr) {
                console.error('[Facturas] detalle HTTP ' + xhr.status,
                    (xhr.responseText || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').slice(0, 300));
                aviso('No se pudo cargar la factura (HTTP ' + xhr.status + ').');
            }
        });
    };

    // ============================================================
    // IMPRIMIR FACTURA EN MODAL
    // ============================================================
    window.imprimirFactura = function (id) {
        if (!id) id = window.jQuery('#modal-detalle-factura').data('factura-id');
        if (!id) return;

        // Cargar el PDF en el iframe
        const url = BASE + '/' + id + '/pdf';
        document.getElementById('pdfViewer').src = url;

        // Abrir el modal
        window.jQuery('#modalPdfFactura').modal('show');
    };

    // Botón para imprimir desde el modal
    document.addEventListener('DOMContentLoaded', function () {
        const btnPrint = document.getElementById('btn-print-iframe');
        if (btnPrint) {
            btnPrint.addEventListener('click', function () {
                const iframe = document.getElementById('pdfViewer');
                if (iframe) {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }
            });
        }
    });
 
    // ============================================================
    // ARRANQUE
    // ============================================================
    esperar(function ($) {
        $(function () {

            if ($.fn.DataTable.isDataTable('#tabla-facturas')) {
                $('#tabla-facturas').DataTable().destroy();
            }
            $.fn.dataTable.ext.errMode = 'none';
            $('#tabla-facturas').on('error.dt', function (e, s, t, msg) { console.error('[Facturas] DataTables:', msg); });

            window.table = $('#tabla-facturas').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('facturas.datatable') }}",
                    data: function (d) {
                        d.desde  = $('#f-desde').val();
                        d.hasta  = $('#f-hasta').val();
                        d.estado = $('#f-estado').val();
                    },
                    error: function (xhr) {
                        console.error('[Facturas] datatable HTTP ' + xhr.status,
                            (xhr.responseText || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').slice(0, 400));
                        aviso((xhr.responseJSON && xhr.responseJSON.message)
                            || ('No se pudo cargar las facturas (HTTP ' + xhr.status + ').'));
                    }
                },
                columns: [
                    { data: 'id',                name: 'id',               className: 'text-center', width: '50px' },
                    { data: 'numero_factura',    name: 'numero_factura' },
                    { data: 'cliente_nombre',    name: 'cliente_nombre',   orderable: false },
                    { data: 'fecha_formato',     name: 'fecha_formato',    className: 'text-center' },
                    { data: 'metodo_pago_label', name: 'metodo_pago_label', className: 'text-center', orderable: false, searchable: false },
                    { data: 'vendedor',          name: 'vendedor',         orderable: false, searchable: false },
                    { data: 'total_formateado',  name: 'total_formateado', className: 'text-right' },
                    { data: 'estado_badge',      name: 'estado_badge',     className: 'text-center', orderable: false, searchable: false },
                    {
                        // Botones dibujados aquí: solo consulta (ver e imprimir)
                        data: null, name: 'acciones', orderable: false, searchable: false,
                        className: 'text-center text-nowrap', width: '100px',
                        render: function (data, type, row) {
                            if (!row.id) return '';
                            return '<button type="button" class="btn btn-info btn-sm btn-ver-factura mr-1" data-id="' + row.id + '" title="Ver detalle"><i class="fas fa-eye"></i></button>'
                                 + '<button type="button" class="btn btn-warning btn-sm btn-pdf-factura" data-id="' + row.id + '" title="Imprimir"><i class="fas fa-print"></i></button>';
                        }
                    }
                ],
                order: [[0, 'desc']],
                language: {
                    processing:     'Procesando...',
                    search:         'Buscar:',
                    lengthMenu:     'Mostrar _MENU_ registros',
                    info:           'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty:      'Mostrando 0 a 0 de 0 registros',
                    infoFiltered:   '(filtrado de _MAX_ registros totales)',
                    loadingRecords: 'Cargando...',
                    zeroRecords:    'No se encontraron resultados',
                    emptyTable:     'No hay facturas para mostrar',
                    paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
                }
            });

            $('#tabla-facturas').on('click', '.btn-ver-factura', function () { window.verFactura($(this).data('id')); });
            $('#tabla-facturas').on('click', '.btn-pdf-factura', function () { window.imprimirFactura($(this).data('id')); });

            // Filtros: recargan la tabla al cambiar
            $('#f-desde, #f-hasta, #f-estado').on('change', function () { window.table.ajax.reload(); });
            $('#btn-limpiar-filtros').on('click', function () {
                $('#f-desde, #f-hasta, #f-estado').val('');
                window.table.ajax.reload();
            });
        });
    });
})();
</script>
@stop