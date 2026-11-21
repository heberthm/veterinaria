@extends('layouts.app')

@section('titulo', 'Inventario - VetFlow')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-archive mr-2"></i>Inventario
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Inventario</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes mr-2"></i>Productos e insumos
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalProducto" onclick="abrirModalNuevoProducto()">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaInventario" class="table table-hover table-striped" style="width:100%; font-size:12.5px;">
                           <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Precio venta</th>
                                    <th>IVA</th>
                                    <th>Stock</th>
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
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL NUEVO/EDITAR PRODUCTO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-box text-primary"></i>
                    <span id="tituloModalProducto">Nuevo Producto</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                              
                <form id="formProducto" action="{{ route('inventario.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="producto_id">
                    
                    <div class="row">
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Código</label>
                                <input type="text" name="codigo" class="form-control" placeholder="Código del producto">
                            </div>
                        </div>
                        <div class="col-8 vc-field">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 vc-field">
                            <div class="form-group">
                                <label>Categoría</label>
                                <select name="categoria_id" class="form-control">
                                    <option value="">Sin categoría</option>
                                    @foreach ($categorias ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6 vc-field">
                            <div class="form-group">
                                <label>Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-control" id="producto_tipo" required>
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                    <option value="paquete">Paquete</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="vc-field">
                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" name="descripcion" class="form-control" placeholder="Descripción del producto">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Precio de compra</label>
                                <input type="number" step="0.01" name="precio_compra" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Precio de venta <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="precio_venta" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>IVA %</label>
                                <input type="number" step="0.01" name="iva_porcentaje" class="form-control" value="19">
                            </div>
                        </div>
                    </div>

                    <div class="row" id="filaStock">
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Stock inicial</label>
                                <input type="number" name="stock" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Stock mínimo</label>
                                <input type="number" name="stock_minimo" class="form-control" value="5">
                            </div>
                        </div>
                        <div class="col-4 vc-field">
                            <div class="form-group">
                                <label>Unidad de medida</label>
                                <input type="text" name="unidad_medida" class="form-control" placeholder="unidad, frasco, kg...">
                            </div>
                        </div>
                    </div>

                    <div id="erroresProducto" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-producto">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL AJUSTE DE STOCK --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalAjusteStock" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Ajustar Stock — <span id="ajuste_nombre_producto"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAjusteStock">
                    @csrf
                    <input type="hidden" id="ajuste_producto_id">
                    
                    <div class="form-group">
                        <label>Tipo de ajuste <span class="text-danger">*</span></label>
                        <select id="ajuste_tipo" class="form-control" required>
                            <option value="entrada">Entrada (sumar al stock)</option>
                            <option value="salida">Salida (restar del stock)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad <span class="text-danger">*</span></label>
                        <input type="number" id="ajuste_cantidad" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Motivo (opcional)</label>
                        <input type="text" id="ajuste_motivo" class="form-control" placeholder="Conteo físico, producto dañado, etc.">
                    </div>

                    <div id="erroresAjuste" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-aplicar-ajuste">
                    <i class="fas fa-check"></i> Aplicar Ajuste
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL PARA VER PRODUCTO (Estilo moderno)                  --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalVerProducto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle del Producto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Contenedor para los datos -->
            <div class="modal-body" id="detalleProductoBody" style="padding: 25px; background-color: #ffffff;">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando...</p>
                </div>
            </div>
            
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    Cerrar
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

    // Evita doble inicialización si el script se carga dos veces
    if (window.__inventarioIniciado) return;
    window.__inventarioIniciado = true;

    const CSRF = '{{ csrf_token() }}';

    // Escapa texto antes de meterlo en HTML
    window.escHtml = function (v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };

    // Espera a que jQuery y DataTables existan (el layout puede cargar este script antes)
    function esperar(cb, n) {
        n = n || 0;
        if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) return cb(window.jQuery);
        if (n > 200) return console.error('[Inventario] jQuery/DataTables no están disponibles');
        setTimeout(function () { esperar(cb, n + 1); }, 50);
    }

    esperar(function ($) {
        $(function () {

            // ============================================================
            // DATATABLE
            // ============================================================
            if ($.fn.DataTable.isDataTable('#tablaInventario')) {
                $('#tablaInventario').DataTable().destroy();
            }

            // Que un error del servidor no muestre el alert feo de DataTables
            $.fn.dataTable.ext.errMode = 'none';
            $('#tablaInventario').on('error.dt', function (e, settings, techNote, message) {
                console.error('[Inventario] DataTables:', message);
            });

            window.tablaInventario = $('#tablaInventario').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('inventario.datatable') }}",
                    error: function (xhr) {
                        console.error('[Inventario] datatable HTTP ' + xhr.status,
                            (xhr.responseText || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').slice(0, 400));
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message)
                                || ('No se pudo cargar el inventario (HTTP ' + xhr.status + ').'), 'error');
                        }
                    }
                },
                columns: [
                    { data: 'codigo',           name: 'codigo',         className: 'text-center' },
                    { data: 'nombre',           name: 'nombre' },
                    // Columnas calculadas: sin búsqueda/orden para no generar "Unknown column" en SQL
                    { data: 'categoria_nombre', name: 'categoria_nombre', orderable: false, searchable: false },
                    { data: 'tipo',             name: 'tipo',           className: 'text-center' },
                    { data: 'precio_venta',     name: 'precio_venta',   className: 'text-right' },
                    { data: 'iva_porcentaje',   name: 'iva_porcentaje', className: 'text-center' },
                    { data: 'stock',            name: 'stock',          className: 'text-center' },
                    { data: 'estado_badge',     name: 'estado_badge',   className: 'text-center', orderable: false, searchable: false },
                    {
                        // Los botones se dibujan aquí, sin depender del HTML que devuelva el controlador
                        data: null, name: 'acciones', orderable: false, searchable: false,
                        className: 'text-center text-nowrap', width: '150px',
                        render: function (data, type, row) {
                            // id de la fila; si el servidor no lo manda, se toma del HTML de "acciones"
                            let id = row.id;
                            if (!id && row.acciones) {
                                const m = String(row.acciones).match(/data-id=["'](\d+)/)
                                       || String(row.acciones).match(/\((\d+)/);
                                if (m) id = m[1];
                            }
                            if (!id) return '';
                            const nombre = escHtml(String(row.nombre || '').replace(/<[^>]*>/g, ''));
                            return '<button type="button" class="btn btn-info btn-sm mr-1" onclick="verProducto(' + id + ')" title="Ver">'
                                 +   '<i class="fas fa-eye"></i></button>'
                                 + '<button type="button" class="btn btn-warning btn-sm btn-editar-producto mr-1" data-id="' + id + '" title="Editar">'
                                 +   '<i class="fas fa-edit"></i></button>'
                                 + '<button type="button" class="btn btn-success btn-sm btn-ajustar-stock mr-1" data-id="' + id + '" data-nombre="' + nombre + '" title="Ajustar stock">'
                                 +   '<i class="fas fa-exchange-alt"></i></button>'
                                 + '<button type="button" class="btn btn-danger btn-sm btn-eliminar-producto" data-id="' + id + '" title="Eliminar">'
                                 +   '<i class="fas fa-trash"></i></button>';
                        }
                    }
                ],
                order: [[1, 'asc']],
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
                    paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' },
                },
            });

            // ============================================================
            // MOSTRAR/OCULTAR STOCK SEGÚN TIPO
            // ============================================================
            $('#producto_tipo').on('change', function () {
                $('#filaStock').toggle($(this).val() === 'producto');
            });

            // ============================================================
            // EDITAR PRODUCTO
            // ============================================================
            function editarProducto(id) {
                if (!id) return console.error('[Inventario] editar: falta el id');
                $.ajax({
                    url: '/inventario/' + id,
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        const p = response.producto || response;
                        // ?? y no || : así un IVA de 0 o un stock mínimo de 0 no se cambian por 19 / 5
                        const v = (x, d) => (x === null || x === undefined ? d : x);

                        $('#tituloModalProducto').html('Editar Producto');
                        $('#producto_id').val(p.id);
                        $('#formProducto [name=codigo]').val(v(p.codigo, ''));
                        $('#formProducto [name=nombre]').val(v(p.nombre, ''));
                        $('#formProducto [name=categoria_id]').val(v(p.categoria_id, ''));
                        $('#formProducto [name=tipo]').val(v(p.tipo, 'producto')).trigger('change');
                        $('#formProducto [name=descripcion]').val(v(p.descripcion, ''));
                        $('#formProducto [name=precio_compra]').val(v(p.precio_compra, 0));
                        $('#formProducto [name=precio_venta]').val(v(p.precio_venta, 0));
                        $('#formProducto [name=iva_porcentaje]').val(v(p.iva_porcentaje, 19));
                        $('#formProducto [name=stock]').val(v(p.stock, 0));
                        $('#formProducto [name=stock_minimo]').val(v(p.stock_minimo, 5));
                        $('#formProducto [name=unidad_medida]').val(v(p.unidad_medida, ''));

                        $('#modalProducto').modal('show');
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'No se pudo cargar el producto', 'error');
                        console.error(xhr);
                    }
                });
            }
            window.editarProducto = editarProducto;

            // ============================================================
            // ELIMINAR PRODUCTO
            // ============================================================
            function eliminarProducto(id) {
                if (!id) return console.error('[Inventario] eliminar: falta el id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Eliminar este producto del inventario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: '/inventario/' + id,
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF },
                        success: function (res) {
                            Swal.fire({ icon: 'success', title: 'Eliminado',
                                text: (res && res.message) || 'Producto eliminado correctamente',
                                timer: 1500, timerProgressBar: true, showConfirmButton: false });
                            window.tablaInventario.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({ icon: 'error', title: 'Error',
                                text: (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo eliminar.',
                                timer: 3000, timerProgressBar: true, showConfirmButton: false });
                        }
                    });
                });
            }
            window.eliminarProducto = eliminarProducto;

            // Id del botón: data-id, data-producto-id o, si no, el número dentro de su onclick
            function idDelBoton(el) {
                const $el = $(el);
                let id = $el.data('id') || $el.data('producto-id') || $el.data('producto')
                      || $el.closest('[data-id]').data('id');
                if (!id) {
                    const m = String($el.attr('onclick') || '').match(/\((\d+)/);
                    if (m) id = m[1];
                }
                if (!id) {   // último recurso: id guardado en la fila de DataTables
                    const fila = window.tablaInventario.row($el.closest('tr')).data();
                    if (fila) id = fila.id;
                }
                return id;
            }

            // Delegado: sirve aunque el controlador use otra clase para los botones
            const SEL_EDITAR   = '.btn-editar-producto, .btn-editar, .btn-edit, .editar-producto, [data-accion="editar"]';
            const SEL_ELIMINAR = '.btn-eliminar-producto, .btn-eliminar, .btn-delete, .eliminar-producto, [data-accion="eliminar"]';

            $('#tablaInventario').on('click', SEL_EDITAR, function (e) {
                if ($(this).attr('onclick')) return;   // ya tiene su propio onclick global
                e.preventDefault();
                editarProducto(idDelBoton(this));
            });
            $('#tablaInventario').on('click', SEL_ELIMINAR, function (e) {
                if ($(this).attr('onclick')) return;
                e.preventDefault();
                eliminarProducto(idDelBoton(this));
            });

            // ============================================================
            // AJUSTAR STOCK
            // ============================================================
            $(document).on('click', '.btn-ajustar-stock', function () {
                $('#formAjusteStock')[0].reset();
                $('#ajuste_producto_id').val($(this).data('id'));
                $('#ajuste_nombre_producto').text($(this).data('nombre'));
                $('#erroresAjuste').empty();
                $('#modalAjusteStock').modal('show');
            });

            $('#btn-aplicar-ajuste').on('click', function () {
                const id = $('#ajuste_producto_id').val();
                const $btn = $(this);

                $('#erroresAjuste').empty();

                $.ajax({
                    url: '/inventario/' + id + '/stock',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF },
                    data: {
                        tipo:     $('#ajuste_tipo').val(),
                        cantidad: $('#ajuste_cantidad').val(),
                        motivo:   $('#ajuste_motivo').val(),
                    },
                    beforeSend: function () {
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Aplicando...');
                    },
                    success: function (res) {
                        $('#modalAjusteStock').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Éxito',
                            text: (res && res.message) || 'Stock ajustado correctamente',
                            timer: 1500, timerProgressBar: true, showConfirmButton: false });
                        window.tablaInventario.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        const j = xhr.responseJSON || {};
                        const msg = j.errors
                            ? Object.values(j.errors).flat().map(escHtml).join('<br>')
                            : escHtml(j.message || 'No se pudo ajustar el stock.');
                        $('#erroresAjuste').html(msg);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Aplicar Ajuste');
                    }
                });
            });

            // ============================================================
            // GUARDAR PRODUCTO (NUEVO/EDITAR)
            // ============================================================
            function guardarProducto() {
                const $btn = $('#btn-guardar-producto');
                if ($btn.prop('disabled')) return;     // evita doble envío

                const id = $('#producto_id').val();
                const url = id ? '/inventario/' + id : '/inventario';
                let data = $('#formProducto').serialize();
                // En un producto nuevo no se manda "id" vacío (podría romper el insert)
                if (!id) data = data.replace(/(^|&)id=[^&]*/, '').replace(/^&/, '');

                $('#erroresProducto').empty();

                $.ajax({
                    url: url,
                    method: id ? 'PUT' : 'POST',
                    data: data,
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    beforeSend: function () {
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                    },
                    success: function (res) {
                        $('#modalProducto').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Éxito',
                            text: (res && res.message) || 'Producto guardado correctamente',
                            timer: 1500, timerProgressBar: true, showConfirmButton: false });
                        window.tablaInventario.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        const j = xhr.responseJSON || {};
                        if (xhr.status === 422 && j.errors) {
                            $('#erroresProducto').html(
                                Object.values(j.errors).flat().map(escHtml).join('<br>')
                            );
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error',
                                text: j.message || ('Error al guardar el producto (HTTP ' + xhr.status + ')'),
                                timer: 3000, timerProgressBar: true, showConfirmButton: false });
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                    }
                });
            }

            $('#btn-guardar-producto').on('click', guardarProducto);

            // Enter dentro del formulario no debe enviarlo de forma normal (recarga de página)
            $('#formProducto').on('submit', function (e) {
                e.preventDefault();
                guardarProducto();
            });

            // ============================================================
            // LIMPIAR FORMULARIOS AL CERRAR MODALES
            // ============================================================
            $('#modalProducto').on('hidden.bs.modal', function () {
                $('#formProducto')[0].reset();
                $('#producto_id').val('');
                $('#erroresProducto').empty();
                $('#filaStock').show();
                $('#tituloModalProducto').text('Nuevo Producto');
                $('#btn-guardar-producto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            });

            $('#modalAjusteStock').on('hidden.bs.modal', function () {
                $('#formAjusteStock')[0].reset();
                $('#erroresAjuste').empty();
                $('#btn-aplicar-ajuste').prop('disabled', false).html('<i class="fas fa-check"></i> Aplicar Ajuste');
            });
        });
    });

    // ============================================================
    // NUEVO PRODUCTO (lo llama el botón con onclick)
    // ============================================================
    window.abrirModalNuevoProducto = function () {
        const $ = window.jQuery;
        $('#formProducto')[0].reset();
        $('#producto_id').val('');
        $('#erroresProducto').empty();
        $('#filaStock').show();
        $('#tituloModalProducto').text('Nuevo Producto');
        $('#btn-guardar-producto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    };

    // ============================================================
    // VER PRODUCTO (lo llama el botón de la tabla con onclick)
    // ============================================================
    window.verProducto = function (id) {
        const $ = window.jQuery;

        $.ajax({
            url: '/inventario/' + id,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                const p = response.producto || response;
                if (!p || !p.id) {
                    Swal.fire('Error', 'No se pudo cargar el producto.', 'error');
                    return;
                }

                const dinero = (n) => '$' + (parseFloat(n) || 0).toLocaleString('es-CO');
                const activo = (p.activo === undefined) ? true : !!Number(p.activo);
                const estadoBadge = activo
                    ? '<span class="badge badge-success" style="padding:4px 10px;border-radius:20px;">Activo</span>'
                    : '<span class="badge badge-secondary" style="padding:4px 10px;border-radius:20px;">Inactivo</span>';
                const bajo = Number(p.stock) <= Number(p.stock_minimo);

                const fila = (icono, etiqueta, valor) => `
                    <div class="d-flex align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                        <div class="mr-3 text-center" style="width:25px;color:#3b82f6;"><i class="${icono}"></i></div>
                        <div style="font-size:13px;"><strong>${etiqueta}:</strong> ${valor}</div>
                    </div>`;

                document.getElementById('detalleProductoBody').innerHTML = `
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3"
                             style="width:65px;height:65px;background:#3b82f6;font-size:24px;font-weight:bold;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 font-weight-bold" style="font-size:22px;">${escHtml(p.nombre)}</h4>
                            <div class="d-flex align-items-center mt-1">
                                <span class="text-muted small mr-2">${escHtml(p.codigo || 'Sin código')}</span>
                                ${estadoBadge}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-info-circle"></i> Información General</h6>
                            ${fila('fas fa-tag', 'Categoría', escHtml(p.categoria ? p.categoria.nombre : '—'))}
                            ${fila('fas fa-layer-group', 'Tipo', escHtml(p.tipo))}
                            ${fila('fas fa-align-left', 'Descripción', escHtml(p.descripcion || '—'))}
                            ${fila('fas fa-ruler', 'Unidad de Medida', escHtml(p.unidad_medida || '—'))}
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-dollar-sign"></i> Precios y Stock</h6>
                            ${fila('fas fa-shopping-cart', 'Precio Compra', dinero(p.precio_compra))}
                            ${fila('fas fa-cash-register', 'Precio Venta', dinero(p.precio_venta))}
                            ${fila('fas fa-percent', 'IVA', escHtml(p.iva_porcentaje) + '%')}
                            ${fila('fas fa-cubes', 'Stock Actual', escHtml(p.stock)
                                + (bajo ? ' <span class="text-danger font-weight-bold">(¡Bajo!)</span>' : ''))}
                        </div>
                    </div>`;

                $('#modalVerProducto').modal('show');
            },
            error: function (xhr) {
                Swal.fire('Error', 'Error al cargar el producto. Código: ' + xhr.status, 'error');
                console.error(xhr);
            }
        });
    };
})();
</script>
@stop