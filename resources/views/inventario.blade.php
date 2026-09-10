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
                        <table class="table table-bordered table-hover" id="tablaInventario">
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

@stop

@section('js')
<script>
$(document).ready(function() {
    // ============================================================
    // DATATABLE
    // ============================================================
    window.tablaInventario = $('#tablaInventario').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('inventario.datatable') }}",
        columns: [
            { data: 'codigo', name: 'codigo', defaultContent: '—' },
            { data: 'nombre', name: 'nombre' },
            { data: 'categoria', name: 'categoria', defaultContent: '—' },
            { data: 'tipo', name: 'tipo', render: function(d) { 
                return d ? d.charAt(0).toUpperCase() + d.slice(1) : '—'; 
            }},
            { data: 'precio_venta', name: 'precio_venta', render: function(d) { 
                return '$' + Number(d).toLocaleString('es-CO'); 
            }},
            { data: 'iva_porcentaje', name: 'iva_porcentaje', render: function(d) { 
                return d + '%'; 
            }},
            { data: 'stock', name: 'stock', render: function(d, t, row) {
                if (row.tipo !== 'producto') return '—';
                return d + ' ' + (row.stock_bajo ? '<span class="badge badge-warning">Bajo</span>' : '');
            }},
            { data: 'activo', name: 'activo', render: function(d) {
                return d 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>';
            }},
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // ============================================================
    // MOSTRAR/OCULTAR STOCK SEGÚN TIPO
    // ============================================================
    $('#producto_tipo').on('change', function() {
        $('#filaStock').toggle($(this).val() === 'producto');
    });

    // ============================================================
    // EDITAR PRODUCTO
    // ============================================================
    $(document).on('click', '.btn-editar-producto', function() {
        var id = $(this).data('id');
        $.get('/inventario/' + id, function(p) {
            $('#tituloModalProducto').html('<i class="fas fa-pen" style="color:var(--vc-blue)"></i> Editar Producto');
            $('#producto_id').val(p.id);
            $('#formProducto [name=codigo]').val(p.codigo);
            $('#formProducto [name=nombre]').val(p.nombre);
            $('#formProducto [name=categoria_id]').val(p.categoria_id);
            $('#formProducto [name=tipo]').val(p.tipo).trigger('change');
            $('#formProducto [name=descripcion]').val(p.descripcion);
            $('#formProducto [name=precio_compra]').val(p.precio_compra);
            $('#formProducto [name=precio_venta]').val(p.precio_venta);
            $('#formProducto [name=iva_porcentaje]').val(p.iva_porcentaje);
            $('#formProducto [name=stock]').val(p.stock);
            $('#formProducto [name=stock_minimo]').val(p.stock_minimo);
            $('#formProducto [name=unidad_medida]').val(p.unidad_medida);
            $('#modalProducto').modal('show');
        });
    });

    // ============================================================
    // ELIMINAR PRODUCTO
    // ============================================================
    $(document).on('click', '.btn-eliminar-producto', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar este producto del inventario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/inventario/' + id,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: res.message || 'Producto eliminado correctamente',
                            confirmButtonColor: '#28a745'
                        });
                        window.tablaInventario.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'No se pudo eliminar.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    // ============================================================
    // AJUSTAR STOCK
    // ============================================================
    $(document).on('click', '.btn-ajustar-stock', function() {
        $('#ajuste_producto_id').val($(this).data('id'));
        $('#ajuste_nombre_producto').text($(this).data('nombre'));
        $('#formAjusteStock')[0].reset();
        $('#erroresAjuste').empty();
        $('#modalAjusteStock').modal('show');
    });

    // ============================================================
    // APLICAR AJUSTE DE STOCK
    // ============================================================
    $('#btn-aplicar-ajuste').on('click', function() {
        var id = $('#ajuste_producto_id').val();
        var formData = {
            _token: '{{ csrf_token() }}',
            tipo: $('#ajuste_tipo').val(),
            cantidad: $('#ajuste_cantidad').val(),
            motivo: $('#ajuste_motivo').val(),
        };

        $.ajax({
            url: '/inventario/' + id + '/stock',
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#btn-aplicar-ajuste').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Aplicando...');
            },
            success: function(res) {
                $('#modalAjusteStock').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: res.message || 'Stock ajustado correctamente',
                    confirmButtonColor: '#28a745'
                });
                window.tablaInventario.ajax.reload();
            },
            error: function(xhr) {
                $('#erroresAjuste').text(xhr.responseJSON?.message || 'No se pudo ajustar el stock.');
            },
            complete: function() {
                $('#btn-aplicar-ajuste').prop('disabled', false).html('<i class="fas fa-check"></i> Aplicar Ajuste');
            }
        });
    });

    // ============================================================
    // GUARDAR PRODUCTO (NUEVO/EDITAR)
    // ============================================================
    $('#btn-guardar-producto').on('click', function() {
        var form = $('#formProducto');
        var id = $('#producto_id').val();
        var url = id ? '/inventario/' + id : '/inventario';
        var method = id ? 'PUT' : 'POST';
        var formData = form.serialize();

        $('#erroresProducto').empty();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#btn-guardar-producto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(res) {
                $('#modalProducto').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: res.message || 'Producto guardado correctamente',
                    confirmButtonColor: '#28a745'
                });
                window.tablaInventario.ajax.reload();
                $('#formProducto')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var mensaje = '';
                    $.each(errors, function(key, value) {
                        mensaje += value[0] + '\n';
                    });
                    $('#erroresProducto').html(mensaje);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al guardar el producto',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function() {
                $('#btn-guardar-producto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    // ============================================================
    // LIMPIAR FORMULARIO AL CERRAR MODAL
    // ============================================================
    $('#modalProducto').on('hidden.bs.modal', function() {
        $('#formProducto')[0].reset();
        $('#producto_id').val('');
        $('#erroresProducto').empty();
        $('#filaStock').show();
        $('#tituloModalProducto').html('<i class="fas fa-box" style="color:var(--vc-green)"></i> Nuevo Producto');
        $('#btn-guardar-producto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });

    $('#modalAjusteStock').on('hidden.bs.modal', function() {
        $('#formAjusteStock')[0].reset();
        $('#erroresAjuste').empty();
        $('#btn-aplicar-ajuste').prop('disabled', false).html('<i class="fas fa-check"></i> Aplicar Ajuste');
    });
});

function abrirModalNuevoProducto() {
    $('#formProducto')[0].reset();
    $('#producto_id').val('');
    $('#erroresProducto').empty();
    $('#filaStock').show();    
    $('#btn-guardar-producto').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
}
</script>
@stop