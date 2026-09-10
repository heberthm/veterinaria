@extends('layouts.app')

@section('titulo', 'Desparasitación - VetFlow')

@php
    // 🔥 VALORES POR DEFECTO
    $mascotas = isset($mascotas) ? $mascotas : collect([]);
    $veterinarios = isset($veterinarios) ? $veterinarios : collect([]);
@endphp

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-pills mr-2"></i>Desparasitación
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Desparasitación</li>
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
                        <i class="fas fa-list mr-2"></i>Registro de Desparasitación
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-desparasitacion">
                            <i class="fas fa-plus"></i> Nueva Desparasitación
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-desparasitaciones">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Propietario</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Veterinario</th>
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
{{-- 🔥 MODAL NUEVA DESPARASITACIÓN --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-desparasitacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-pills text-primary"></i>
                    Nueva Desparasitación
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Complete los datos de la desparasitación.
                </div>
                
                <form id="form-desparasitacion" action="{{ route('desparasitaciones.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota <span class="text-danger">*</span></label>
                                <select name="mascota_id" class="form-control select2" required>
                                    <option value="">Seleccionar mascota...</option>
                                    @if($mascotas->isEmpty())
                                        <option value="" disabled>No hay mascotas registradas</option>
                                    @else
                                        @foreach($mascotas as $mascota)
                                            <option value="{{ $mascota->id }}">
                                                {{ $mascota->nombre }} - {{ $mascota->cliente->nombreCompleto() ?? 'Sin cliente' }}
                                            </option>
                                        @endforeach
                                    @endif
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
                                <label>Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Desparasitante Interno" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="interna">Interna</option>
                                    <option value="externa">Externa</option>
                                    <option value="mixta">Mixta</option>
                                </select>
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
                                <label>Próxima Dosis</label>
                                <input type="date" name="fecha_proxima" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Lote</label>
                                <input type="text" name="lote" class="form-control" placeholder="Número de lote">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Laboratorio</label>
                                <input type="text" name="laboratorio" class="form-control" placeholder="Ej: Bayer">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dosis</label>
                                <input type="text" name="dosis" class="form-control" placeholder="Ej: 1 tableta">
                            </div>
                        </div>
                        <div class="col-md-4">
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
                <button type="button" class="btn btn-primary" id="btn-guardar-desparasitacion">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETALLE --}}
<div class="modal fade" id="modal-detalle-desparasitacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-pills mr-2"></i> Detalle de Desparasitación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detalle-desparasitacion-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarDesparasitacion()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarDesparasitacion()">
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
    
    window.tablaDesparasitaciones = $('#tabla-desparasitaciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('desparasitaciones.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'mascota_nombre', name: 'mascota.nombre' },
            { data: 'cliente_nombre', name: 'mascota.cliente.nombres' },
            { data: 'nombre', name: 'nombre' },
            { data: 'tipo_label', name: 'tipo', className: 'text-center' },
            { data: 'fecha_aplicacion_formato', name: 'fecha_aplicacion', className: 'text-center' },
            { data: 'veterinario_nombre', name: 'veterinario.name' },
            { data: 'estado_badge', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
    
    $('#btn-guardar-desparasitacion').on('click', function() {
        var form = $('#form-desparasitacion');
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn-guardar-desparasitacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-desparasitacion').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Desparasitación guardada correctamente',
                        confirmButtonColor: '#28a745'
                    });
                    window.tablaDesparasitaciones.ajax.reload();
                    $('#form-desparasitacion')[0].reset();
                    $('.select2').val('').trigger('change');
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
                }
            },
            complete: function() {
                $('#btn-guardar-desparasitacion').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
    
    $('#modal-desparasitacion').on('hidden.bs.modal', function() {
        $('#form-desparasitacion')[0].reset();
        $('.select2').val('').trigger('change');
        $('#btn-guardar-desparasitacion').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });
});

function verDesparasitacion(id) {
    $('#modal-detalle-desparasitacion-body').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);
    
    $.ajax({
        url: '/desparasitaciones/' + id + '/detalle',
        method: 'GET',
        success: function(data) {
            $('#modal-detalle-desparasitacion-body').html(data);
            $('#modal-detalle-desparasitacion').data('desparasitacion-id', id);
        }
    });
    
    $('#modal-detalle-desparasitacion').modal('show');
}

function editarDesparasitacion() {
    var id = $('#modal-detalle-desparasitacion').data('desparasitacion-id');
    if (id) {
        $('#modal-detalle-desparasitacion').modal('hide');
        window.location.href = '/desparasitaciones/' + id + '/editar';
    }
}

function eliminarDesparasitacion() {
    var id = $('#modal-detalle-desparasitacion').data('desparasitacion-id');
    if (id) {
        $('#modal-detalle-desparasitacion').modal('hide');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar este registro de desparasitación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/desparasitaciones/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'Desparasitación eliminada correctamente',
                                confirmButtonColor: '#28a745'
                            });
                            window.tablaDesparasitaciones.ajax.reload();
                        }
                    }
                });
            }
        });
    }
}
</script>
@stop