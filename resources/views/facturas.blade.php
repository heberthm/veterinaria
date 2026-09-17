@extends('layouts.app')


@section('title', 'Facturación')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-file-invoice mr-2"></i>Facturación
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Facturación</li>
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
                    <i class="fas fa-list mr-1"></i>Listado de Facturas
                </h3>
                <div class="card-tools">
                    @can('crear_facturas')
                    <button type="button" class="btn btn-success btn-sm" onclick="abrirModalCrearFactura()">
                        <i class="fas fa-plus"></i> Nueva Factura
                    </button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabla-facturas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>Saldo</th>
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

<!-- Modal para Factura (Crear) -->
<div class="modal fade" id="modal-factura" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice text-primary"></i>
                    <span id="modal-factura-titulo">Nueva Factura</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando...</p>
                </div>
            </div>
            <div class="modal-footer" style="display:none;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-factura">
                    <i class="fas fa-save"></i> Generar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalle de Factura -->
<div class="modal fade" id="modal-detalle-factura" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice mr-2"></i> Detalle de Factura
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="imprimirFactura()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarFactura()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago -->
<div class="modal fade" id="modal-pago" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave mr-2"></i> Registrar Pago
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-registrar-pago">
                    <i class="fas fa-check"></i> Registrar Pago
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
$(document).ready(function() {
    window.table = $('#tabla-facturas').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('facturas.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'numero_factura', name: 'numero_factura' },
            { data: 'cliente_nombre', name: 'cliente.nombres' },
            { data: 'fecha_emision_formato', name: 'fecha_emision', className: 'text-center' },
            { data: 'tipo', name: 'tipo', className: 'text-center' },
            { data: 'total_formateado', name: 'total', className: 'text-right' },
            { data: 'saldo_formateado', name: 'saldo', className: 'text-right' },
            { data: 'estado_badge', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { targets: [0], width: '50px' },
            { targets: [8], width: '150px' }
        ]
    });
});

function abrirModalCrearFactura() {
    abrirModalCrear('{{ route('facturas.crear') }}', '#modal-factura');
}

function verFactura(id) {
    abrirModalDetalle('{{ url('/facturas') }}/' + id + '/detalle', '#modal-detalle-factura');
    $('#modal-detalle-factura').data('factura-id', id);
}

function registrarPago(id) {
    $.ajax({
        url: '{{ url('/facturas') }}/' + id + '/pago',
        method: 'GET',
        success: function(data) {
            $('#modal-pago .modal-body').html(data);
            $('#modal-pago').modal('show');
            $('#modal-pago').data('factura-id', id);
        },
        error: function() {
            mostrarError('Error al cargar el formulario de pago');
        }
    });
}

function eliminarFactura() {
    const id = $('#modal-detalle-factura').data('factura-id');
    if (id) {
        $('#modal-detalle-factura').modal('hide');
        confirmarEliminar('{{ url('/facturas') }}/' + id, '¿Eliminar esta factura?');
    }
}

function imprimirFactura() {
    const id = $('#modal-detalle-factura').data('factura-id');
    if (id) {
        window.open('{{ url('/facturas') }}/' + id + '/pdf', '_blank');
    }
}

// Registrar pago
$(document).on('click', '#btn-registrar-pago', function() {
    const id = $('#modal-pago').data('factura-id');
    const form = $('#form-pago');
    const data = form.serialize();
    
    $.ajax({
        url: '{{ url('/facturas') }}/' + id + '/pago',
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#modal-pago').modal('hide');
                mostrarExito(response.message || 'Pago registrado exitosamente');
                window.table.ajax.reload();
            } else {
                mostrarError(response.message || 'Error al registrar pago');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                let mensaje = '';
                $.each(errors, function(key, value) {
                    mensaje += value[0] + '\n';
                });
                mostrarError(mensaje);
            } else {
                mostrarError('Error al registrar el pago');
            }
        }
    });
});
</script>
@stop