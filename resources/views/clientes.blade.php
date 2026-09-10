@extends('layouts.app')

@section('titulo', 'Clientes - VetCloud')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-users mr-2"></i>Clientes
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Clientes</li>
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
                        <i class="fas fa-list mr-2"></i>Listado de Clientes
                    </h3>
                    <div class="card-tools">
                        {{-- 🔥 BOTÓN QUE ABRE EL MODAL --}}
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-cliente">
                            <i class="fas fa-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-clientes">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Documento</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Mascotas</th>
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
{{-- MODAL NUEVO CLIENTE --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-cliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus text-primary"></i>
                    Nuevo Cliente
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                              
                <form id="form-cliente" action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                    <option value="NIT">NIT</option>
                                    <option value="PA">Pasaporte</option>
                                    <option value="RC">Registro Civil</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Documento <span class="text-danger">*</span></label>
                                <input type="text" name="numero_documento" class="form-control" placeholder="Ej: 123456789" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombres" class="form-control" placeholder="Nombres del cliente" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" class="form-control" placeholder="Apellidos del cliente" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" name="celular" class="form-control" placeholder="Ej: 3001234567">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input type="text" name="direccion" class="form-control" placeholder="Dirección del cliente">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" placeholder="Ciudad">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Barrio</label>
                                <input type="text" name="barrio" class="form-control" placeholder="Barrio">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Género</label>
                                <select name="genero" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" name="activo" class="custom-control-input" id="activo-switch" checked>
                                    <label class="custom-control-label" for="activo-switch">Activo</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notas</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-cliente">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL DETALLE DE CLIENTE --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-detalle-cliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i> Detalle de Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detalle-cliente-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="editarCliente()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarCliente()">
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
    // DATATABLE
    // ============================================================
    window.tablaClientes = $('#tabla-clientes').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('clientes.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'documento', name: 'documento' },
            { data: 'nombres', name: 'nombres' },
            { data: 'apellidos', name: 'apellidos' },
            { data: 'telefono', name: 'telefono' },
            { data: 'email', name: 'email' },
            { data: 'mascotas_count', name: 'mascotas_count', className: 'text-center' },
            { data: 'estado', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
    
    // ============================================================
    // GUARDAR CLIENTE (DESDE EL MODAL)
    // ============================================================
    $('#btn-guardar-cliente').on('click', function() {
        var form = $('#form-cliente');
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#btn-guardar-cliente').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-cliente').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Cliente guardado correctamente',
                        confirmButtonColor: '#28a745'
                    });
                    window.tablaClientes.ajax.reload();
                    
                    // Limpiar el formulario
                    $('#form-cliente')[0].reset();
                    
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
                        text: 'Error al guardar el cliente',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function() {
                $('#btn-guardar-cliente').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
    
    // ============================================================
    // LIMPIAR FORMULARIO AL CERRAR EL MODAL
    // ============================================================
    $('#modal-cliente').on('hidden.bs.modal', function() {
        $('#form-cliente')[0].reset();
        $('#btn-guardar-cliente').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });
});

// ============================================================
// FUNCIONES PARA CLIENTES
// ============================================================

function verCliente(id) {
    $('#modal-detalle-cliente-body').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Cargando detalles...</p>
        </div>
    `);
    
    $.ajax({
        url: '/clientes/' + id + '/detalle',
        method: 'GET',
        success: function(data) {
            $('#modal-detalle-cliente-body').html(data);
            $('#modal-detalle-cliente').data('cliente-id', id);
        },
        error: function() {
            $('#modal-detalle-cliente-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error al cargar los detalles.
                </div>
            `);
        }
    });
    
    $('#modal-detalle-cliente').modal('show');
}

function editarCliente() {
    var id = $('#modal-detalle-cliente').data('cliente-id');
    if (id) {
        $('#modal-detalle-cliente').modal('hide');
        window.location.href = '/clientes/' + id + '/editar';
    }
}

function eliminarCliente() {
    var id = $('#modal-detalle-cliente').data('cliente-id');
    if (id) {
        $('#modal-detalle-cliente').modal('hide');
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar este cliente y todas sus mascotas?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/clientes/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message || 'Cliente eliminado correctamente',
                                confirmButtonColor: '#28a745'
                            });
                            window.tablaClientes.ajax.reload();
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
                            text: xhr.responseJSON?.message || 'Error al eliminar el cliente',
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