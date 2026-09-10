@extends('layouts.app')

@section('title', 'Citas - VetCloud')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-list mr-2"></i>Citas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Citas</li>
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
                    <i class="fas fa-calendar-check mr-1"></i>Listado de Citas
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="abrirModalCrear('{{ route('citas.crear') }}', '#modal-cita')">
                        <i class="fas fa-plus"></i> Nueva Cita
                    </button>
                    <a href="{{ route('agenda') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-alt"></i> Ver Agenda
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabla-citas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Mascota</th>
                                <th>Cliente</th>
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

<!-- Modal para Cita (Crear/Editar) -->
<div class="modal fade" id="modal-cita" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus text-primary"></i>
                    <span id="modal-cita-titulo">Nueva Cita</span>
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
                <button type="button" class="btn btn-primary" id="btn-guardar-cita">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalle de Cita -->
<div class="modal fade" id="modal-detalle-cita" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i> Detalle de Cita
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
                <button type="button" class="btn btn-warning" onclick="editarCita()">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button type="button" class="btn btn-danger" onclick="eliminarCita()">
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
    // Inicializar DataTable
    window.table = $('#tabla-citas').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('citas.datatable') }}",
        columns: [
            { data: 'id', name: 'id', className: 'text-center', width: '50px' },
            { data: 'fecha', name: 'fecha', className: 'text-center' },
            { data: 'hora', name: 'hora', className: 'text-center' },
            { data: 'mascota', name: 'mascota' },
            { data: 'cliente', name: 'cliente' },
            { data: 'veterinario', name: 'veterinario' },
            { data: 'estado', name: 'estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { targets: [0], width: '50px' },
            { targets: [7], width: '140px' }
        ]
    });
});

// Función para ver detalle
function verCita(id) {
    abrirModalDetalle('{{ url('/citas') }}/' + id + '/detalle', '#modal-detalle-cita');
    $('#modal-detalle-cita').data('cita-id', id);
}

// Función para editar cita
function editarCita(id) {
    const citaId = id || $('#modal-detalle-cita').data('cita-id');
    if (citaId) {
        $('#modal-detalle-cita').modal('hide');
        abrirModalEditar('{{ url('/citas') }}/' + citaId + '/editar', '#modal-cita');
    }
}

// Función para eliminar cita
function eliminarCita(id) {
    const citaId = id || $('#modal-detalle-cita').data('cita-id');
    if (citaId) {
        $('#modal-detalle-cita').modal('hide');
        confirmarEliminar('{{ url('/citas') }}/' + citaId, '¿Eliminar esta cita?');
    }
}

// Función para cambiar estado de cita
function cambiarEstadoCita(id, estado) {
    Swal.fire({
        title: 'Cambiar estado',
        text: '¿Desea cambiar el estado de esta cita?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url('/citas') }}/' + id + '/estado',
                method: 'PUT',
                data: { estado: estado },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        mostrarExito('Estado actualizado correctamente');
                        window.table.ajax.reload();
                    } else {
                        mostrarError(response.message || 'Error al actualizar estado');
                    }
                },
                error: function() {
                    mostrarError('Error al cambiar el estado');
                }
            });
        }
    });
}
</script>
@stop