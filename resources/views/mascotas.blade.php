@extends('layouts.app')

@section('titulo', 'Mascotas - VetCloud')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-paw mr-2"></i>Mascotas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Mascotas</li>
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
                        <i class="fas fa-list mr-2"></i>Listado de Mascotas
                    </h3>
                    <div class="card-tools">
                        {{-- 🔥 BOTÓN QUE ABRE EL MODAL --}}
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-mascota">
                            <i class="fas fa-plus"></i> Nueva Mascota
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-mascotas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Especie</th>
                                    <th>Raza</th>
                                    <th>Género</th>
                                    <th>Edad</th>
                                    <th>Propietario</th>
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
{{-- 🔥 MODAL NUEVA MASCOTA (CONTENIDO COMPLETO INTEGRADO) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-mascota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paw text-primary"></i>
                    Nueva Mascota
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- ========================================================== --}}
                {{-- FORMULARIO COMPLETO - INTEGRADO EN LA VISTA --}}
                {{-- ========================================================== --}}
               
                
                <form id="form-mascota" action="{{ route('mascotas.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Propietario <span class="text-danger">*</span></label>
                                <select name="cliente_id" class="form-control select2" required>
                                    <option value="">Seleccionar propietario...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->nombres }} {{ $cliente->apellidos }} - {{ $cliente->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" placeholder="Nombre de la mascota" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Especie <span class="text-danger">*</span></label>
                                <select name="especie" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="perro">🐕 Perro</option>
                                    <option value="gato">🐈 Gato</option>
                                    <option value="ave">🐦 Ave</option>
                                    <option value="roedor">🐹 Roedor</option>
                                    <option value="reptil">🦎 Reptil</option>
                                    <option value="equino">🐴 Equino</option>
                                    <option value="bovino">🐄 Bovino</option>
                                    <option value="porcino">🐷 Porcino</option>
                                    <option value="conejo">🐰 Conejo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Raza</label>
                                <input type="text" name="raza" class="form-control" placeholder="Raza de la mascota">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" name="color" class="form-control" placeholder="Color de la mascota">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Género <span class="text-danger">*</span></label>
                                <select name="genero" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="macho">♂️ Macho</option>
                                    <option value="hembra">♀️ Hembra</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" name="peso" class="form-control" step="0.01" min="0" max="200" placeholder="Ej: 5.5">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Número de Chip</label>
                                <input type="text" name="numero_chip" class="form-control" placeholder="978101082776321">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="activo" class="custom-control-input" id="activo-switch" checked>
                                    <label class="custom-control-label" for="activo-switch">Activo</label>
                                </div>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="esterilizado" class="custom-control-input" id="esterilizado-switch">
                                    <label class="custom-control-label" for="esterilizado-switch">Esterilizado</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Alergias</label>
                                <textarea name="alergias" class="form-control" rows="2" placeholder="Lista de alergias..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Enfermedades Crónicas</label>
                                <textarea name="enfermedades_cronicas" class="form-control" rows="2" placeholder="Enfermedades crónicas..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                    </div>
                </form>
                {{-- ========================================================== --}}
                {{-- FIN DEL FORMULARIO --}}
                {{-- ========================================================== --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-mascota">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL DETALLE DE MASCOTA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-detalle-mascota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-paw mr-2"></i> Detalle de Mascota
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detalle-mascota-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarMascota()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarMascota()">
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
    // SELECT2 - Inicialización
    // ============================================================
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar propietario...',
            allowClear: true
        });
    }
    
    // ============================================================
    // DATATABLE
    // ============================================================
    window.tablaMascotas = $('#tabla-mascotas').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mascotas.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'nombre', name: 'nombre' },
            { data: 'especie_label', name: 'especie' },
            { data: 'raza', name: 'raza' },
            { data: 'genero_label', name: 'genero' },
            { data: 'edad', name: 'edad' },
            { data: 'cliente_nombre', name: 'cliente.nombres' },
            { data: 'estado', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
    
    // ============================================================
    // GUARDAR MASCOTA (DESDE EL MODAL)
    // ============================================================
    $('#btn-guardar-mascota').on('click', function() {
        var form = $('#form-mascota');
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn-guardar-mascota').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-mascota').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Mascota guardada correctamente',
                        confirmButtonColor: '#28a745'
                    });
                    window.tablaMascotas.ajax.reload();
                    
                    // Limpiar el formulario
                    $('#form-mascota')[0].reset();
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
                        text: 'Error al guardar la mascota',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function() {
                $('#btn-guardar-mascota').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
    
    // ============================================================
    // LIMPIAR FORMULARIO AL CERRAR EL MODAL
    // ============================================================
    $('#modal-mascota').on('hidden.bs.modal', function() {
        $('#form-mascota')[0].reset();
        $('.select2').val('').trigger('change');
        $('#btn-guardar-mascota').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });
});

// ============================================================
// FUNCIONES PARA MASCOTAS
// ============================================================

function verMascota(id) {
    $('#modal-detalle-mascota-body').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);
    
    $.ajax({
        url: '/mascotas/' + id + '/detalle',
        method: 'GET',
        success: function(data) {
            $('#modal-detalle-mascota-body').html(data);
            $('#modal-detalle-mascota').data('mascota-id', id);
        },
        error: function() {
            $('#modal-detalle-mascota-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error al cargar los detalles.
                </div>
            `);
        }
    });
    
    $('#modal-detalle-mascota').modal('show');
}

function editarMascota() {
    var id = $('#modal-detalle-mascota').data('mascota-id');
    if (id) {
        $('#modal-detalle-mascota').modal('hide');
        window.location.href = '/mascotas/' + id + '/editar';
    }
}

function eliminarMascota() {
    var id = $('#modal-detalle-mascota').data('mascota-id');
    if (id) {
        $('#modal-detalle-mascota').modal('hide');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar esta mascota y todos sus registros?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/mascotas/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'Mascota eliminada correctamente',
                                confirmButtonColor: '#28a745'
                            });
                            window.tablaMascotas.ajax.reload();
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
                            text: xhr.responseJSON?.message || 'Error al eliminar la mascota',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    }
}
</script>
@stop