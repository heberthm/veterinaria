@extends('layouts.app')

@section('titulo', 'Compras - VetFlow')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-shopping-bag mr-2"></i>Compras
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Compras</li>
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
                        <i class="fas fa-list mr-2"></i>Registro de Compras
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-compra">
                            <i class="fas fa-plus"></i> Nueva Compra
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-compras">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>N° Factura</th>
                                    <th>Proveedor</th>
                                    <th>Fecha</th>
                                    <th>Vencimiento</th>
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
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL NUEVA COMPRA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-compra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-bag text-primary"></i>
                    <span id="modal-compra-titulo">Nueva Compra</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                                
                <form id="form-compra" action="{{ route('compras.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Proveedor <span class="text-danger">*</span></label>
                                <select name="proveedor_id" class="form-control select2" required>
                                    <option value="">Seleccionar proveedor...</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">
                                            {{ $proveedor->nombre }} - {{ $proveedor->nit ?? 'Sin NIT' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Factura</label>
                                <input type="text" name="numero_factura" class="form-control" placeholder="Número de factura">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Compra <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_compra" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Método de Pago</label>
                                <select name="metodo_pago" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5><i class="fas fa-boxes mr-1"></i> Productos</h5>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tabla-productos-compra">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>IVA</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="productos-body">
                                        <!-- Productos agregados dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info btn-sm" onclick="agregarProductoCompra()">
                                <i class="fas fa-plus"></i> Agregar Producto
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td class="text-right" id="subtotal-display">$ 0</td>
                                </tr>
                                <tr>
                                    <td><strong>IVA (19%)</strong></td>
                                    <td class="text-right" id="iva-display">$ 0</td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="text-right text-success" id="total-display"><strong>$ 0</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>

                    <div id="erroresCompra" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-compra">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL DETALLE DE COMPRA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-detalle-compra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-bag mr-2"></i> Detalle de Compra
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detalle-compra-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarCompra()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarCompra()">
                    <i class="fas fa-trash"></i> Eliminar
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
    // SELECT2
    // ============================================================
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar proveedor...',
            allowClear: true
        });
    }
    
    // ============================================================
    // DATATABLE
    // ============================================================
    window.tablaCompras = $('#tabla-compras').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('compras.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'numero_factura', name: 'numero_factura' },
            { data: 'proveedor_nombre', name: 'proveedor.nombre' },
            { data: 'fecha_compra_formato', name: 'fecha_compra', className: 'text-center' },
            { data: 'fecha_vencimiento', name: 'fecha_vencimiento', className: 'text-center' },
            { data: 'total_formateado', name: 'total', className: 'text-right' },
            { data: 'estado_badge', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});

// ============================================================
// FUNCIONES PARA COMPRAS
// ============================================================

var productosCompra = [];
var contadorProductoCompra = 0;

function agregarProductoCompra() {
    var productoId = prompt('ID del producto:');
    if (!productoId) return;
    
    var cantidad = prompt('Cantidad:', '1');
    if (!cantidad) return;
    
    var precio = prompt('Precio unitario:', '0');
    if (precio === null) return;
    
    // Buscar el producto en la lista de productos disponibles
    var productos = @json($productos ?? []);
    var producto = productos.find(p => p.id == productoId);
    
    if (!producto) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Producto no encontrado',
            confirmButtonColor: '#d33'
        });
        return;
    }
    
    var item = {
        id: contadorProductoCompra++,
        producto_id: productoId,
        nombre: producto.nombre,
        cantidad: parseFloat(cantidad),
        precio: parseFloat(precio),
        descuento: 0,
        iva: parseFloat(producto.iva_porcentaje) || 19
    };
    
    productosCompra.push(item);
    renderizarProductosCompra();
    calcularTotalesCompra();
}

function renderizarProductosCompra() {
    var tbody = $('#productos-body');
    tbody.empty();
    
    var html = '';
    productosCompra.forEach(function(item, index) {
        var subtotal = item.cantidad * item.precio;
        var ivaItem = subtotal * (item.iva / 100);
        var totalItem = subtotal + ivaItem;
        
        html += `
            <tr>
                <td>
                    <input type="text" class="form-control form-control-sm" 
                           value="${item.nombre}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.cantidad}" min="0.01" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'cantidad', parseFloat(this.value))">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.precio}" min="0" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'precio', parseFloat(this.value))">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.descuento}" min="0" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'descuento', parseFloat(this.value))">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.iva}" min="0" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'iva', parseFloat(this.value))">
                </td>
                <td class="text-right">$ ${subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarProductoCompra(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    tbody.html(html);
}

function actualizarProductoCompra(index, campo, valor) {
    productosCompra[index][campo] = valor;
    calcularTotalesCompra();
    renderizarProductosCompra();
}

function eliminarProductoCompra(index) {
    productosCompra.splice(index, 1);
    renderizarProductosCompra();
    calcularTotalesCompra();
}

function calcularTotalesCompra() {
    var subtotal = 0;
    var iva = 0;
    
    productosCompra.forEach(function(item) {
        var subtotalItem = item.cantidad * item.precio;
        subtotal += subtotalItem;
        iva += subtotalItem * (item.iva / 100);
    });
    
    var total = subtotal + iva;
    
    $('#subtotal-display').text('$ ' + subtotal.toFixed(2));
    $('#iva-display').text('$ ' + iva.toFixed(2));
    $('#total-display').text('$ ' + total.toFixed(2));
}

function verCompra(id) {
    $('#modal-detalle-compra-body').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);
    
    $.ajax({
        url: '/compras/' + id + '/detalle',
        method: 'GET',
        success: function(data) {
            $('#modal-detalle-compra-body').html(data);
            $('#modal-detalle-compra').data('compra-id', id);
        },
        error: function() {
            $('#modal-detalle-compra-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error al cargar los detalles.
                </div>
            `);
        }
    });
    
    $('#modal-detalle-compra').modal('show');
}

function editarCompra() {
    var id = $('#modal-detalle-compra').data('compra-id');
    if (id) {
        $('#modal-detalle-compra').modal('hide');
        window.location.href = '/compras/' + id + '/editar';
    }
}

function eliminarCompra() {
    var id = $('#modal-detalle-compra').data('compra-id');
    if (id) {
        $('#modal-detalle-compra').modal('hide');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar esta compra?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/compras/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'Compra eliminada correctamente',
                                confirmButtonColor: '#28a745'
                            });
                            window.tablaCompras.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error al eliminar',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Error al eliminar la compra',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    }
}

// ============================================================
// GUARDAR COMPRA
// ============================================================
$('#btn-guardar-compra').on('click', function() {
    if (productosCompra.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Agregue al menos un producto a la compra',
            confirmButtonColor: '#d33'
        });
        return;
    }
    
    var form = $('#form-compra');
    var formData = form.serialize();
    
    // Agregar productos al formulario
    var productosData = productosCompra.map(function(item) {
        return {
            producto_id: item.producto_id,
            cantidad: item.cantidad,
            precio: item.precio,
            descuento: item.descuento || 0,
            iva: item.iva || 19
        };
    });
    
    $.ajax({
        url: form.attr('action'),
        method: form.attr('method'),
        data: formData + '&' + $.param({ productos: productosData }),
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function() {
            $('#btn-guardar-compra').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal-compra').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message || 'Compra guardada correctamente',
                    confirmButtonColor: '#28a745'
                });
                window.tablaCompras.ajax.reload();
                $('#form-compra')[0].reset();
                productosCompra = [];
                renderizarProductosCompra();
                calcularTotalesCompra();
                $('.select2').val('').trigger('change');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al guardar',
                    confirmButtonColor: '#d33'
                });
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON?.errors;
            if (errors) {
                var mensaje = '';
                $.each(errors, function(key, value) {
                    mensaje += value[0] + '\n';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: mensaje,
                    confirmButtonColor: '#d33'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la compra',
                    confirmButtonColor: '#d33'
                });
            }
        },
        complete: function() {
            $('#btn-guardar-compra').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
        }
    });
});

// ============================================================
// LIMPIAR FORMULARIO AL CERRAR MODAL
// ============================================================
$('#modal-compra').on('hidden.bs.modal', function() {
    $('#form-compra')[0].reset();
    $('.select2').val('').trigger('change');
    productosCompra = [];
    renderizarProductosCompra();
    calcularTotalesCompra();
    $('#btn-guardar-compra').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    $('#erroresCompra').empty();
});
</script>
@stop