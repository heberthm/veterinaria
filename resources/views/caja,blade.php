@extends('layouts.app')

@section('titulo', 'Caja - VetFlow')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-money-bill-wave mr-2"></i>Caja
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Caja</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Resumen de Caja --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Saldo Actual</span>
                    <span class="info-box-number" id="saldo-actual">
                        $ {{ number_format($caja->saldo_actual ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Ingresos</span>
                    <span class="info-box-number" id="total-ingresos">$ 0</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Egresos</span>
                    <span class="info-box-number" id="total-egresos">$ 0</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Estado</span>
                    <span class="info-box-number" id="estado-caja">
                        <span class="badge badge-{{ $caja->estaAbierta() ? 'success' : 'danger' }}">
                            {{ $caja->estaAbierta() ? 'ABIERTA' : 'CERRADA' }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Botones de acción --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>Acciones de Caja
                    </h3>
                    <div class="card-tools">
                        @if($caja->estaAbierta())
                            <button type="button" class="btn btn-danger btn-sm" onclick="cerrarCaja()">
                                <i class="fas fa-times"></i> Cerrar Caja
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="abrirModalIngreso()">
                                <i class="fas fa-plus"></i> Ingreso
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="abrirModalEgreso()">
                                <i class="fas fa-minus"></i> Egreso
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" onclick="abrirModalApertura()">
                                <i class="fas fa-plus"></i> Abrir Caja
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Movimientos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>Movimientos de Caja
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-movimientos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Saldo</th>
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
{{-- MODAL APERTURA DE CAJA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-apertura" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Abrir Caja
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-apertura">
                    @csrf
                    <input type="hidden" name="caja_id" value="{{ $caja->id }}">
                    <div class="form-group">
                        <label>Saldo Inicial <span class="text-danger">*</span></label>
                        <input type="number" name="saldo_inicial" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                    <div id="errores-apertura" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarApertura()">
                    <i class="fas fa-check"></i> Abrir Caja
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL INGRESO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-ingreso" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Registrar Ingreso
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-ingreso">
                    @csrf
                    <input type="hidden" name="caja_id" value="{{ $caja->id }}">
                    <div class="form-group">
                        <label>Monto <span class="text-danger">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría <span class="text-danger">*</span></label>
                        <select name="categoria" class="form-control" required>
                            <option value="venta">Venta</option>
                            <option value="factura">Factura</option>
                            <option value="abono">Abono</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripción <span class="text-danger">*</span></label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Método de Pago</label>
                        <select name="metodo_pago" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta_credito">Tarjeta de Crédito</option>
                            <option value="tarjeta_debito">Tarjeta Débito</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="nequi">Nequi</option>
                            <option value="daviplata">DaviPlata</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text" name="referencia" class="form-control">
                    </div>
                    <div id="errores-ingreso" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarIngreso()">
                    <i class="fas fa-check"></i> Registrar Ingreso
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL EGRESO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-egreso" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-minus mr-2"></i>Registrar Egreso
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-egreso">
                    @csrf
                    <input type="hidden" name="caja_id" value="{{ $caja->id }}">
                    <div class="form-group">
                        <label>Monto <span class="text-danger">*</span></label>
                        <input type="number" name="monto" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría <span class="text-danger">*</span></label>
                        <select name="categoria" class="form-control" required>
                            <option value="gasto">Gasto</option>
                            <option value="retiro">Retiro</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripción <span class="text-danger">*</span></label>
                        <input type="text" name="descripcion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Método de Pago</label>
                        <select name="metodo_pago" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta_credito">Tarjeta de Crédito</option>
                            <option value="tarjeta_debito">Tarjeta Débito</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="nequi">Nequi</option>
                            <option value="daviplata">DaviPlata</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text" name="referencia" class="form-control">
                    </div>
                    <div id="errores-egreso" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="guardarEgreso()">
                    <i class="fas fa-check"></i> Registrar Egreso
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
    window.tablaMovimientos = $('#tabla-movimientos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('caja.datatable') }}",
            data: function(d) {
                d.caja_id = {{ $caja->id }};
            }
        },
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'fecha', name: 'created_at' },
            { data: 'usuario_nombre', name: 'usuario.name' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'categoria_label', name: 'categoria' },
            { data: 'tipo_label', name: 'tipo' },
            { data: 'monto_formateado', name: 'monto', className: 'text-right' },
            { data: 'saldo_nuevo', name: 'saldo_nuevo', className: 'text-right' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // ============================================================
    // ACTUALIZAR RESUMEN
    // ============================================================
    actualizarResumen();

    function actualizarResumen() {
        $.ajax({
            url: "{{ route('caja.resumen') }}",
            method: 'GET',
            data: { caja_id: {{ $caja->id }} },
            success: function(data) {
                $('#saldo-actual').text('$ ' + Number(data.saldo_actual).toLocaleString('es-CO'));
                $('#total-ingresos').text('$ ' + Number(data.total_ingresos).toLocaleString('es-CO'));
                $('#total-egresos').text('$ ' + Number(data.total_egresos).toLocaleString('es-CO'));
            }
        });
    }
});

// ============================================================
// FUNCIONES PARA APERTURA
// ============================================================
function abrirModalApertura() {
    $('#form-apertura')[0].reset();
    $('#errores-apertura').empty();
    $('#modal-apertura').modal('show');
}

function guardarApertura() {
    var form = $('#form-apertura');
    var formData = form.serialize();
    
    $.ajax({
        url: "{{ route('caja.abrir') }}",
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function() {
            $('#modal-apertura .btn-success').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Abriendo...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal-apertura').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                });
                setTimeout(function() { location.reload(); }, 1500);
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON?.errors;
            if (errors) {
                var mensaje = '';
                $.each(errors, function(key, value) {
                    mensaje += value[0] + '\n';
                });
                $('#errores-apertura').html(mensaje);
            } else {
                $('#errores-apertura').html(xhr.responseJSON?.message || 'Error al abrir la caja.');
            }
        },
        complete: function() {
            $('#modal-apertura .btn-success').prop('disabled', false).html('<i class="fas fa-check"></i> Abrir Caja');
        }
    });
}

// ============================================================
// FUNCIONES PARA INGRESO
// ============================================================
function abrirModalIngreso() {
    $('#form-ingreso')[0].reset();
    $('#errores-ingreso').empty();
    $('#modal-ingreso').modal('show');
}

function guardarIngreso() {
    var form = $('#form-ingreso');
    var formData = form.serialize();
    
    $.ajax({
        url: "{{ route('caja.ingreso') }}",
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function() {
            $('#modal-ingreso .btn-success').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal-ingreso').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                });
                window.tablaMovimientos.ajax.reload();
                actualizarResumen();
                $('#saldo-actual').text('$ ' + Number(response.saldo_actual).toLocaleString('es-CO'));
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON?.errors;
            if (errors) {
                var mensaje = '';
                $.each(errors, function(key, value) {
                    mensaje += value[0] + '\n';
                });
                $('#errores-ingreso').html(mensaje);
            } else {
                $('#errores-ingreso').html(xhr.responseJSON?.message || 'Error al registrar ingreso.');
            }
        },
        complete: function() {
            $('#modal-ingreso .btn-success').prop('disabled', false).html('<i class="fas fa-check"></i> Registrar Ingreso');
        }
    });
}

// ============================================================
// FUNCIONES PARA EGRESO
// ============================================================
function abrirModalEgreso() {
    $('#form-egreso')[0].reset();
    $('#errores-egreso').empty();
    $('#modal-egreso').modal('show');
}

function guardarEgreso() {
    var form = $('#form-egreso');
    var formData = form.serialize();
    
    $.ajax({
        url: "{{ route('caja.egreso') }}",
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function() {
            $('#modal-egreso .btn-danger').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal-egreso').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                });
                window.tablaMovimientos.ajax.reload();
                actualizarResumen();
                $('#saldo-actual').text('$ ' + Number(response.saldo_actual).toLocaleString('es-CO'));
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON?.errors;
            if (errors) {
                var mensaje = '';
                $.each(errors, function(key, value) {
                    mensaje += value[0] + '\n';
                });
                $('#errores-egreso').html(mensaje);
            } else {
                $('#errores-egreso').html(xhr.responseJSON?.message || 'Error al registrar egreso.');
            }
        },
        complete: function() {
            $('#modal-egreso .btn-danger').prop('disabled', false).html('<i class="fas fa-check"></i> Registrar Egreso');
        }
    });
}

// ============================================================
// CERRAR CAJA
// ============================================================
function cerrarCaja() {
    Swal.fire({
        title: '¿Cerrar caja?',
        text: 'Se generará el reporte de cierre',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cerrar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('caja.cerrar') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    caja_id: {{ $caja->id }}
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Caja cerrada',
                            text: 'Saldo final: $' + Number(response.saldo_final).toLocaleString('es-CO'),
                            confirmButtonColor: '#28a745'
                        });
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al cerrar la caja',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

// ============================================================
// VER MOVIMIENTO
// ============================================================
function verMovimiento(id) {
    // Implementar modal de detalle si es necesario
    Swal.fire({
        icon: 'info',
        title: 'Detalle del Movimiento',
        text: 'ID: ' + id,
        confirmButtonColor: '#3085d6'
    });
}
</script>
@stop