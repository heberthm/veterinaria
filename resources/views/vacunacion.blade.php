@extends('layouts.app')

@section('titulo', 'Vacunación - VetFlow')´

@php
    // 🔥 SI LA VARIABLE NO EXISTE, CREAR UN ARRAY VACÍO
    $veterinarios = isset($veterinarios) ? $veterinarios : collect([]);
    $mascotas = isset($mascotas) ? $mascotas : collect([]);
@endphp

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-syringe mr-2"></i>Vacunación
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Vacunación</li>
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
                        <i class="fas fa-list mr-2"></i>Registro de Vacunación
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-vacuna">
                            <i class="fas fa-plus"></i> Nueva Vacuna
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-vacunas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Propietario</th>
                                    <th>Vacuna</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Veterinario</th>
                                    <th>Vigente</th>
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
{{-- 🔥 MODAL NUEVA VACUNA (CONTENIDO COMPLETO INTEGRADO) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-vacuna" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-syringe text-primary"></i>
                    Nueva Vacuna
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Complete los datos de la vacunación.
                </div>
                
                <form id="form-vacuna" action="{{ route('vacunas.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota <span class="text-danger">*</span></label>
                                <select name="mascota_id" class="form-control select2" required>
                                    <option value="">Seleccionar mascota...</option>
                                    @foreach($mascotas as $mascota)
                                        <option value="{{ $mascota->id }}">
                                            {{ $mascota->nombre }} - {{ $mascota->cliente->nombreCompleto() ?? 'Sin cliente' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Veterinario <span class="text-danger">*</span></label>
                                <select name="veterinario_id" class="form-control" required>
                                    <option value="">Seleccionar veterinario...</option>
                                    @if($veterinarios->isEmpty())
                                        <option value="" disabled>No hay veterinarios registrados</option>
                                    @else
                                        @foreach($veterinarios as $veterinario)
                                            <option value="{{ $veterinario->id }}">
                                                Dr. {{ $veterinario->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre de la Vacuna <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Vacuna Antirrábica" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Laboratorio</label>
                                <input type="text" name="laboratorio" class="form-control" placeholder="Ej: Pfizer">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Aplicación <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_aplicacion" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                                <label>Próxima Dosis</label>
                                <input type="date" name="fecha_proxima" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lote</label>
                                <input type="text" name="lote" class="form-control" placeholder="Número de lote">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vía de Aplicación</label>
                                <select name="via_aplicacion" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="subcutanea">Subcutánea</option>
                                    <option value="intramuscular">Intramuscular</option>
                                    <option value="intradermica">Intradérmica</option>
                                    <option value="oral">Oral</option>
                                    <option value="nasal">Nasal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dosis</label>
                                <input type="text" name="dosis" class="form-control" placeholder="Ej: 1 ml">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Costo ($)</label>
                                <input type="number" name="costo" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-vacuna">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETALLE --}}
<div class="modal fade" id="modal-detalle-vacuna" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-syringe mr-2"></i> Detalle de Vacuna
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detalle-vacuna-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarVacuna()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarVacuna()">
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
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar mascota...',
            allowClear: true
        });
    }
    
    window.tablaVacunas = $('#tabla-vacunas').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('vacunas.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'mascota_nombre', name: 'mascota.nombre' },
            { data: 'cliente_nombre', name: 'mascota.cliente.nombres' },
            { data: 'nombre', name: 'nombre' },
            { data: 'fecha_aplicacion_formato', name: 'fecha_aplicacion', className: 'text-center' },
            { data: 'veterinario_nombre', name: 'veterinario.name' },
            { data: 'vigente', name: 'vigente', className: 'text-center' },
            { data: 'estado_badge', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
    
    $('#btn-guardar-vacuna').on('click', function() {
        var form = $('#form-vacuna');
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn-guardar-vacuna').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-vacuna').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Vacuna guardada correctamente',
                        confirmButtonColor: '#28a745'
                    });
                    window.tablaVacunas.ajax.reload();
                    $('#form-vacuna')[0].reset();
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
                        text: 'Error al guardar la vacuna',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function() {
                $('#btn-guardar-vacuna').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
    
    $('#modal-vacuna').on('hidden.bs.modal', function() {
        $('#form-vacuna')[0].reset();
        $('.select2').val('').trigger('change');
        $('#btn-guardar-vacuna').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });
});

function verVacuna(id) {
    $('#modal-detalle-vacuna-body').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);
    
    $.ajax({
        url: '/vacunas/' + id + '/detalle',
        method: 'GET',
        success: function(data) {
            $('#modal-detalle-vacuna-body').html(data);
            $('#modal-detalle-vacuna').data('vacuna-id', id);
        },
        error: function() {
            $('#modal-detalle-vacuna-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error al cargar los detalles.
                </div>
            `);
        }
    });
    
    $('#modal-detalle-vacuna').modal('show');
}

function editarVacuna() {
    var id = $('#modal-detalle-vacuna').data('vacuna-id');
    if (id) {
        $('#modal-detalle-vacuna').modal('hide');
        window.location.href = '/vacunas/' + id + '/editar';
    }
}

function eliminarVacuna() {
    var id = $('#modal-detalle-vacuna').data('vacuna-id');
    if (id) {
        $('#modal-detalle-vacuna').modal('hide');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar este registro de vacunación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/vacunas/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'Vacuna eliminada correctamente',
                                confirmButtonColor: '#28a745'
                            });
                            window.tablaVacunas.ajax.reload();
                        }
                    }
                });
            }
        });
    }
}
</script>
@stop