@extends('layouts.app')
´
@extends('adminlte::page')

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
                        @if($caja->estaAbierta())
                            <span class="badge badge-success">Caja Abierta</span>
                        @else
                            <span class="badge badge-danger">Caja Cerrada</span>
                        @endif
                        <span class="vc-text-orange ml-2">
                            {{ $caja->nombre }} — saldo actual ${{ number_format($caja->saldo_actual ?? 0, 0, ',', '.') }}
                        </span>
                    </h3>
                    <div class="card-tools">
                        @if($caja->estaAbierta())
                            <button type="button" class="btn btn-danger btn-sm" onclick="abrirModalCerrarCaja()">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Caja
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
                    <table class="table table-bordered table-hover" id="tabla-movimientos" style="width:100%">
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
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="apertura_caja_id" value="{{ $caja->id ?? 1 }}">

</div>


@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('=== CAJA VISTA CARGADA ===');

    if ($('#tabla-movimientos').length > 0) {
        $('#tabla-movimientos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('caja.datatable') }}",
                data: function(d) {
                    d.caja_id = $('#apertura_caja_id').val();
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
                { data: 'saldo_nuevo', name: 'saldo_nuevo', className: 'text-right' }
            ],
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    }
});

function abrirModalCerrarCaja() {
    var cajaId = $('#apertura_caja_id').val();
    if (!cajaId) return;

    Swal.fire({
        icon: 'warning',
        title: '¿Confirmas el cierre de caja?',
        text: 'Se calculará el saldo final con los movimientos registrados.',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Sí, cerrar caja',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            ejecutarCierreCaja(cajaId);
        }
    });
}

function ejecutarCierreCaja(cajaId) {
    Swal.fire({
        title: 'Cerrando caja...',
        text: 'Calculando saldo final',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: function() { Swal.showLoading(); }
    });

    $.ajax({
        url: "{{ route('caja.cerrar') }}",
        method: 'POST',
        data: { _token: '{{ csrf_token() }}', caja_id: cajaId },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Caja Cerrada!',
                    text: 'Saldo final: $' + Number(response.saldo_final).toLocaleString('es-CO'),
                    confirmButtonColor: '#28a745'
                }).then(function() { location.reload(); });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Error inesperado.',
                confirmButtonColor: '#d33'
            });
        }
    });
}

function abrirModalIngreso() { $('#modal-ingreso').modal('show'); }
function abrirModalEgreso() { $('#modal-egreso').modal('show'); }
function abrirModalApertura() { $('#modal-apertura').modal('show'); }

function guardarApertura() {
    var form = $('#form-apertura');
    $.ajax({
        url: "{{ route('caja.abrir') }}",
        method: 'POST',
        data: form.serialize(),
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modal-apertura').modal('hide');
                Swal.fire({ icon: 'success', title: '¡Caja Abierta!', text: response.message });
                setTimeout(function() { location.reload(); }, 1500);
            }
        },
        error: function(xhr) {
            var mensaje = 'Error.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            $('#errores-apertura').html(mensaje);
        }
    });
}

function guardarIngreso() {
    var form = $('#form-ingreso');
    $.ajax({
        url: "{{ route('caja.ingreso') }}",
        method: 'POST',
        data: form.serialize(),
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modal-ingreso').modal('hide');
                Swal.fire({ icon: 'success', title: '¡Ingreso Registrado!', text: response.message });
                setTimeout(function() { location.reload(); }, 1500);
            }
        },
        error: function(xhr) {
            var mensaje = 'Error.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            $('#errores-ingreso').html(mensaje);
        }
    });
}

function guardarEgreso() {
    var form = $('#form-egreso');
    $.ajax({
        url: "{{ route('caja.egreso') }}",
        method: 'POST',
        data: form.serialize(),
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('#modal-egreso').modal('hide');
                Swal.fire({ icon: 'success', title: '¡Egreso Registrado!', text: response.message });
                setTimeout(function() { location.reload(); }, 1500);
            }
        },
        error: function(xhr) {
            var mensaje = 'Error.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            $('#errores-egreso').html(mensaje);
        }
    });
}
</script>
@endpush