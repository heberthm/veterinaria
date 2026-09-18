@extends('layouts.app')

@section('title', 'Caja - VetFlow')

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

    {{-- ==================== RESUMEN ==================== --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if(isset($caja) && $caja->estaAbierta())
                            <span class="badge badge-success">Caja Abierta</span>
                        @else
                            <span class="badge badge-danger">Caja Cerrada</span>
                        @endif
                        <span style="color: #FF8C00; font-weight: 600; margin-left: 10px;">
                            {{ $caja->nombre ?? 'Caja Principal' }} — 
                            saldo actual ${{ number_format($caja->saldo_actual ?? 0, 0, ',', '.') }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        @if(isset($caja) && $caja->estaAbierta())
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalCerrarCaja">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Caja
                            </button>

                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalIngreso">
                                <i class="fas fa-plus"></i> Ingreso
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalEgreso">
                                <i class="fas fa-minus"></i> Egreso
                            </button>
                        @else
                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    data-toggle="modal"
                                    data-target="#modalAbrirCaja">
                                <i class="fas fa-plus"></i> Abrir Caja
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== MOVIMIENTOS ==================== --}}
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
                    <table class="table table-hover" id="tabla-movimientos" " style="width:100%; font-size:12.5px;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                    </table>´
                  </div> 
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 INPUT OCULTO CON EL ID DE LA CAJA --}}
    <input type="hidden" id="caja_id_actual" value="{{ $caja->id ?? 1 }}">

</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL ABRIR CAJA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog">
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
            <form id="formAbrirCaja">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="caja_id" value="{{ $caja->id ?? 1 }}">

                    <div class="form-group">
                        <label>Saldo Inicial <span class="text-danger">*</span></label>
                        <input type="number"
                               name="saldo_inicial"
                               class="form-control"
                               step="100"
                               min="0"
                               value="0"
                               required>
                        <small class="text-muted">Monto en efectivo al iniciar el día</small>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Opcional..."></textarea>
                    </div>

                    <div id="erroresAbrirCaja" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarApertura()">
                        <i class="fas fa-check"></i> Abrir Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL CERRAR CAJA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalCerrarCaja" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Caja
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formCerrarCaja">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="caja_id" value="{{ $caja->id ?? 1 }}">

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¿Confirmas el cierre de caja?</strong><br>
                        Se calculará el saldo final con los movimientos registrados.
                    </div>

                    <div class="form-group">
                        <label>Monto Contado en Caja (efectivo real)</label>
                        <input type="number"
                               name="monto_cierre"
                               class="form-control"
                               step="100"
                               min="0"
                               placeholder="Ingresa el efectivo contado">
                        <small class="text-muted">Opcional: sirve para detectar diferencias</small>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones_cierre" class="form-control" rows="2" placeholder="Opcional..."></textarea>
                    </div>

                    <div id="erroresCerrarCaja" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="guardarCierre()">
                        <i class="fas fa-check"></i> Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL INGRESO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalIngreso" tabindex="-1" role="dialog">
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
            <form id="formIngreso">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="caja_id" value="{{ $caja->id ?? 1 }}">

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
                        <input type="text" name="descripcion" class="form-control" placeholder="Describe el ingreso" required>
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
                        <input type="text" name="referencia" class="form-control" placeholder="Opcional">
                    </div>

                    <div id="erroresIngreso" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarIngreso()">
                        <i class="fas fa-check"></i> Registrar Ingreso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL EGRESO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalEgreso" tabindex="-1" role="dialog">
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
            <form id="formEgreso">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="caja_id" value="{{ $caja->id ?? 1 }}">

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
                        <input type="text" name="descripcion" class="form-control" placeholder="Describe el egreso" required>
                    </div>

                    <div class="form-group">
                        <label>Método de Pago</label>
                        <select name="metodo_pago" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta_credito">Tarjeta de Crédito</option>
                            <option value="tarjeta_debito">Tarjeta de Débito</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="nequi">Nequi</option>
                            <option value="daviplata">DaviPlata</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Referencia</label>
                        <input type="text" name="referencia" class="form-control" placeholder="Opcional">
                    </div>

                    <div id="erroresEgreso" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="guardarEgreso()">
                        <i class="fas fa-check"></i> Registrar Egreso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

{{-- ============================================================ --}}
{{-- 🔥 SCRIPTS --}}
{{-- ============================================================ --}}

@push('js')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
// ============================================================
// 🔥 VARIABLE GLOBAL DEL DATATABLE
// ============================================================
var tablaMovimientos = null;

$(document).ready(function() {
    console.log('=== CAJA VISTA CARGADA ===');


    if ($('#tabla-movimientos').length === 0) {
        console.error('❌ La tabla #tabla-movimientos no existe');
        return;
    }

    if (typeof $.fn.DataTable === 'undefined') {
        console.error('❌ DataTables NO está cargado');
        return;
    }

    // 🔥 INICIALIZAR DATATABLE
    window.tablaMovimientos = $('#tabla-movimientos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('caja.datatable') }}",
            type: 'GET',
            data: function(d) {
                d.caja_id = $('#caja_id_actual').val();
                console.log('Enviando AJAX:', d); // DEBUG
            },
            dataSrc: function(json) {
                console.log('Respuesta del servidor:', json); // DEBUG
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error('❌ Error AJAX:', xhr.status, xhr.responseText);
            }
        },
        columns: [
           
            { data: 'fecha', name: 'created_at' },
            { data: 'usuario_nombre', name: 'usuario.name' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'categoria_label', name: 'categoria' },
            { data: 'tipo_label', name: 'tipo' },
            { data: 'monto_formateado', name: 'monto', className: 'text-right' },
            { data: 'saldo_nuevo', name: 'saldo_nuevo', className: 'text-right' }
        ],
        order: [[0, 'desc']],
       "language": {
            "emptyTable": "No hay clientes registrados.",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        order: [[0, 'desc']],
    });

    console.log('✅ DataTable inicializado');
});

// ============================================================
// 🔥 FUNCIONES AUXILIARES
// ============================================================
function actualizarSaldoHeader(nuevoSaldo) {
    if (nuevoSaldo === undefined || nuevoSaldo === null) return;
    
    var textoFormateado = '$ ' + Number(nuevoSaldo).toLocaleString('es-CO');
    
    $('.card-title span[style*="FF8C00"]').each(function() {
        var texto = $(this).text();
        var nuevoTexto = texto.replace(/\$[\d.,]+/g, textoFormateado);
        $(this).text(nuevoTexto);
    });
    
    console.log('✅ Saldo actualizado a:', textoFormateado);
}

function actualizarEstadoCaja(nuevoEstado, saldoFinal) {
    var badge = $('.card-title .badge');
    
    if (nuevoEstado === 'cerrada') {
        badge.removeClass('badge-success').addClass('badge-danger').text('Caja Cerrada');
    } else {
        badge.removeClass('badge-danger').addClass('badge-success').text('Caja Abierta');
    }
    
    if (saldoFinal !== undefined) {
        actualizarSaldoHeader(saldoFinal);
    }
    
    var cardTools = $('.card-tools');
    
    if (nuevoEstado === 'cerrada') {
        cardTools.html(`
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAbrirCaja">
                <i class="fas fa-plus"></i> Abrir Caja
            </button>
        `);
    } else {
        cardTools.html(`
            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalCerrarCaja">
                <i class="fas fa-sign-out-alt"></i> Cerrar Caja
            </button>
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalIngreso">
                <i class="fas fa-plus"></i> Ingreso
            </button>
            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEgreso">
                <i class="fas fa-minus"></i> Egreso
            </button>
        `);
    }
}

// ============================================================
// 🔥 GUARDAR APERTURA
// ============================================================
function guardarApertura() {
    var form = $('#formAbrirCaja');
    var formData = form.serialize();
    
    $('#erroresAbrirCaja').html('');

    $.ajax({
        url: "{{ route('caja.abrir') }}",
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        beforeSend: function() {
            $('#modalAbrirCaja button.btn-success').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Abriendo...');
        },
        success: function(response) {
            if (response.success) {
                $('#modalAbrirCaja').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Caja Abierta!',
                    text: response.message || 'La caja se abrió correctamente',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
                
                // 🔥 ACTUALIZAR SIN RECARGAR
                if (tablaMovimientos) {
                    tablaMovimientos.ajax.reload(null, false);
                }
                actualizarEstadoCaja('abierta', response.caja?.saldo_actual);
                form[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo abrir la caja'
                });
            }
        },
        error: function(xhr) {
            var mensaje = 'Error al abrir la caja.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = '';
                $.each(xhr.responseJSON.errors, function(key, value) {
                    mensaje += value[0] + '<br>';
                });
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            $('#erroresAbrirCaja').html(mensaje);
        },
        complete: function() {
            $('#modalAbrirCaja button.btn-success').prop('disabled', false)
                .html('<i class="fas fa-check"></i> Abrir Caja');
        }
    });
}

// ============================================================
// 🔥 GUARDAR CIERRE
// ============================================================
function guardarCierre() {
    var form = $('#formCerrarCaja');
    var formData = form.serialize();
    
    $('#erroresCerrarCaja').html('');

    $.ajax({
        url: "{{ route('caja.cerrar') }}",
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        beforeSend: function() {
            $('#modalCerrarCaja button.btn-danger').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Cerrando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modalCerrarCaja').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Caja Cerrada!',
                    html: 'Saldo final: <strong>$' + Number(response.saldo_final).toLocaleString('es-CO') + '</strong>',
                    confirmButtonColor: '#28a745'
                });
                
                // 🔥 ACTUALIZAR SIN RECARGAR
                if (tablaMovimientos) {
                    tablaMovimientos.ajax.reload(null, false);
                }
                actualizarEstadoCaja('cerrada', response.saldo_final);
                form[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo cerrar la caja'
                });
            }
        },
        error: function(xhr) {
            var mensaje = 'Error al cerrar la caja.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = '';
                $.each(xhr.responseJSON.errors, function(key, value) {
                    mensaje += value[0] + '<br>';
                });
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            $('#erroresCerrarCaja').html(mensaje);
        },
        complete: function() {
            $('#modalCerrarCaja button.btn-danger').prop('disabled', false)
                .html('<i class="fas fa-check"></i> Cerrar Caja');
        }
    });
}

// ============================================================
// 🔥 GUARDAR INGRESO
// ============================================================
function guardarIngreso() {
    var form = $('#formIngreso');
    var formData = form.serialize();
    
    $('#erroresIngreso').html('');

    $.ajax({
        url: "{{ route('caja.ingreso') }}",
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        beforeSend: function() {
            $('#modalIngreso button.btn-success').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modalIngreso').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Ingreso Registrado!',
                    text: response.message || 'El ingreso se registró correctamente',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
                
                // 🔥 ACTUALIZAR SIN RECARGAR
                if (tablaMovimientos) {
                    tablaMovimientos.ajax.reload(null, false);
                }
                if (response.saldo_actual !== undefined) {
                    actualizarSaldoHeader(response.saldo_actual);
                }
                form[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo registrar el ingreso'
                });
            }
        },
        error: function(xhr) {
            var mensaje = 'Error al registrar el ingreso.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = '';
                $.each(xhr.responseJSON.errors, function(key, value) {
                    mensaje += value[0] + '<br>';
                });
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            $('#erroresIngreso').html(mensaje);
        },
        complete: function() {
            $('#modalIngreso button.btn-success').prop('disabled', false)
                .html('<i class="fas fa-check"></i> Registrar Ingreso');
        }
    });
}

// ============================================================
// 🔥 GUARDAR EGRESO
// ============================================================
function guardarEgreso() {
    var form = $('#formEgreso');
    var formData = form.serialize();
    
    $('#erroresEgreso').html('');

    $.ajax({
        url: "{{ route('caja.egreso') }}",
        method: 'POST',
        data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        beforeSend: function() {
            $('#modalEgreso button.btn-danger').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modalEgreso').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Egreso Registrado!',
                    text: response.message || 'El egreso se registró correctamente',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                });
                
                // 🔥 ACTUALIZAR SIN RECARGAR
                if (tablaMovimientos) {
                    tablaMovimientos.ajax.reload(null, false);
                }
                if (response.saldo_actual !== undefined) {
                    actualizarSaldoHeader(response.saldo_actual);
                }
                form[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo registrar el egreso'
                });
            }
        },
        error: function(xhr) {
            var mensaje = 'Error al registrar el egreso.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = '';
                $.each(xhr.responseJSON.errors, function(key, value) {
                    mensaje += value[0] + '<br>';
                });
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            $('#erroresEgreso').html(mensaje);
        },
        complete: function() {
            $('#modalEgreso button.btn-danger').prop('disabled', false)
                .html('<i class="fas fa-check"></i> Registrar Egreso');
        }
    });
}

// ============================================================
// 🔥 LIMPIAR FORMULARIOS AL CERRAR MODALES
// ============================================================
$('#modalAbrirCaja, #modalCerrarCaja, #modalIngreso, #modalEgreso').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $(this).find('.text-danger').empty();
});
</script>
@endpush