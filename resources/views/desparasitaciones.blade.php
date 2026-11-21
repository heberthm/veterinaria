@extends('layouts.app')

@section('titulo', 'Desparasitación - VetFlow')

@php
    $mascotas = isset($mascotas) ? $mascotas : collect([]);
    $veterinarios = isset($veterinarios) ? $veterinarios : collect([]);
    $desparasitantes = isset($desparasitantes) ? $desparasitantes : collect([]);
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
                        <table id="tabla-desparasitaciones" class="table table-hover table-striped" style="width:100%; font-size:12.5px;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Propietario</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Veterinario</th>
                                    <th>Próxima dosis</th>
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
                <form id="form-desparasitacion" action="{{ route('desparasitaciones.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota <span class="text-danger">*</span></label>
                                {{-- 🔥 CORRECCIÓN: Agregamos el ID select-mascota --}}
                                <select name="mascota_id" id="select-mascota" class="form-control select2" required style="width:100%">
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
                                <select name="usuario_id" id="select-veterinario" class="form-control select2" required style="width:100%">
                                    <option value="">Seleccionar veterinario...</option>
                                    @if($veterinarios->isEmpty())
                                        <option value="" disabled>No hay veterinarios registrados</option>
                                    @else
                                        @foreach($veterinarios as $veterinario)
                                            <option value="{{ $veterinario->id }}">
                                                Dr. {{ $veterinario->nombre }} {{ $veterinario->apellidos }}
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
                                <select name="desparasitante_id" id="select-desparasitante" class="form-control select2" required style="width:100%">
                                    <option value="">Seleccionar producto...</option>
                                    @if($desparasitantes->isEmpty())
                                        <option value="" disabled>No hay productos registrados</option>
                                    @else
                                        @foreach($desparasitantes as $desp)
                                            <option value="{{ $desp->id }}" data-tipo="{{ $desp->tipo }}" data-laboratorio="{{ $desp->laboratorio }}">
                                                {{ $desp->nombre }} - {{ $desp->laboratorio }} (Stock: {{ $desp->stock }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
                                <label>Vía de Aplicación</label>
                                <select name="via_aplicacion" class="form-control">
                                    <option value="oral">Oral</option>
                                    <option value="topica">Tópica</option>
                                    <option value="subcutanea">Subcutánea</option>
                                    <option value="intramuscular">Intramuscular</option>
                                </select>
                            </div>
                        </div>

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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Costo ($)</label>
                                <input type="number" name="costo" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>
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

{{-- MODAL VER DETALLE (Sin botones de acción) --}}
<div class="modal fade" id="modal-ver-desparasitacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle de Desparasitación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; background-color: #ffffff;">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                         style="width: 65px; height: 65px; background-color: #3b82f6; font-size: 24px; font-weight: bold; margin-right: 15px;">
                        <span id="ver-desp-iniciales">M</span>
                    </div>
                    <div>
                        <h4 id="ver-desp-mascota" style="margin: 0; font-weight: 700; color: #1e293b; font-size: 22px;">Mascota</h4>
                        <div class="d-flex align-items-center mt-1">
                            <span id="ver-desp-cliente" style="color: #64748b; font-size: 14px; margin-right: 10px;">Dueño</span>
                            <span id="ver-desp-estado" class="badge" style="background-color: #22c55e; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px;">Aplicada</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #1e293b; font-weight: 700; font-size: 15px;">
                            <i class="fas fa-paw" style="color: #64748b;"></i> Mascota y Dueño
                        </h6>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-user"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Propietario:</strong> <span id="ver-desp-dueño">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-dog"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Especie/Raza:</strong> <span id="ver-desp-especie">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-user-md"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Veterinario:</strong> <span id="ver-desp-veterinario">--</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #1e293b; font-weight: 700; font-size: 15px;">
                            <i class="fas fa-pills" style="color: #64748b;"></i> Información
                        </h6>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-capsules"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Producto:</strong> <span id="ver-desp-producto">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-check"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Aplicación:</strong> <span id="ver-desp-fecha">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-alt"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Próxima Dosis:</strong> <span id="ver-desp-proxima">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDITAR (Sin salir de la vista) --}}
<div class="modal fade" id="modal-editar-desparasitacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #f59e0b; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;"><i class="fas fa-edit mr-2"></i> Editar Desparasitación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 25px; background-color: #ffffff;">
        <form id="form-editar-desparasitacion">
            @csrf
            <input type="hidden" name="id" id="edit_desp_id">
            
            <!-- 🔥 CAMPOS OCULTOS REQUERIDOS POR EL CONTROLADOR -->
            <input type="hidden" name="mascota_id" id="edit_desp_mascota_id">
            <input type="hidden" name="usuario_id" id="edit_desp_usuario_id">
            <input type="hidden" name="desparasitante_id" id="edit_desp_desparasitante_id">
            <input type="hidden" name="via_aplicacion" value="oral">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;">Mascota</label>
                        <input type="text" id="edit_desp_mascota" class="form-control" readonly style="background-color: #f8fafc;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label style="font-weight: 600;">Producto</label>
                        <input type="text" id="edit_desp_producto" class="form-control" readonly style="background-color: #f8fafc;">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Fecha Aplicación <span class="text-danger">*</span></label>
                        <!-- 🔥 El input de fecha es obligatorio y debe tener name="fecha_aplicacion" -->
                        <input type="date" name="fecha_aplicacion" id="edit_desp_fecha_aplicacion" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Fecha Próxima</label>
                        <input type="date" name="fecha_proxima" id="edit_desp_fecha_proxima" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Estado</label>
                        <select name="estado" id="edit_desp_estado" class="form-control" required>
                            <option value="aplicada">Aplicada</option>
                            <option value="programada">Programada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Lote</label>
                        <input type="text" name="lote" id="edit_desp_lote" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Dosis</label>
                        <input type="number" name="dosis_aplicada" id="edit_desp_dosis" class="form-control" step="0.01">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label style="font-weight: 600;">Costo</label>
                        <input type="number" name="costo" id="edit_desp_costo" class="form-control" step="0.01">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label style="font-weight: 600;">Observaciones</label>
                <textarea name="observaciones" id="edit_desp_observaciones" class="form-control" rows="2"></textarea>
            </div>
        </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btn-actualizar-desparasitacion" style="background-color: #f59e0b; border: none; border-radius: 8px; padding: 8px 20px; font-weight: 600; color: white;">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

@stop

{{-- 🔥 ESTILOS SELECT2 --}}
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
        background-color: #fff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px !important;
        color: #495057;
        font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container--default .select2-results__option {
        font-size: 13px !important;
    }
    .select2-container--open { z-index: 9999 !important; }
    .select2-dropdown { z-index: 9999 !important; }
</style>
@endpush

@section('js')
<script>
$(document).ready(function() {
    if (window.__desparasitacionesInit) return;
    window.__desparasitacionesInit = true;

    // ============================================================
    // 🔥 SELECT2
    // ============================================================
    if ($.fn.select2) {
        $('#select-mascota').select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Buscar mascota...', allowClear: true, dropdownParent: $('#modal-desparasitacion') });
        $('#select-veterinario').select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Buscar veterinario...', allowClear: true, dropdownParent: $('#modal-desparasitacion') });
        $('#select-desparasitante').select2({ theme: 'bootstrap4', width: '100%', placeholder: 'Buscar producto...', allowClear: true, dropdownParent: $('#modal-desparasitacion') });
    }

    // ============================================================
    // 🔥 DATATABLE
    // ============================================================
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabla-desparasitaciones')) {
        $('#tabla-desparasitaciones').DataTable().destroy();
    }

    window.tablaDesparasitaciones = $('#tabla-desparasitaciones').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('desparasitaciones.datatable') }}",
        columns: [
            { data: 'id', name: 'a.id', className: 'text-center', width: '50px' },
            { data: 'mascota_nombre', name: 'm.nombre' },
            { data: 'cliente_nombre', name: 'cliente_nombre' },
            { data: 'producto', name: 'p.nombre' },
            { data: 'tipo_label', name: 'p.tipo', className: 'text-center' },
            { data: 'fecha_aplicacion_formato', name: 'a.fecha_aplicacion', className: 'text-center' },
            { data: 'veterinario_nombre', name: 'u.name' },
            { data: 'fecha_proxima_formato', name: 'a.fecha_proxima', className: 'text-center' },
            { data: 'estado_badge', name: 'a.estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // ============================================================
    // 🔥 GUARDAR
    // ============================================================
    $('#btn-guardar-desparasitacion').off('click').on('click', function() {
        var form = $('#form-desparasitacion');
        var formData = form.serialize();
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend: function() {
                $('#btn-guardar-desparasitacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-desparasitacion').modal('hide');
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: response.message, showConfirmButton: false, timer: 1500 });
                    window.tablaDesparasitaciones.ajax.reload(null, false);
                    $('#form-desparasitacion')[0].reset();
                    $('#select-mascota, #select-veterinario, #select-desparasitante').val('').trigger('change');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                var msg = errors ? Object.values(errors).flat().join('\n') : (xhr.responseJSON?.message || 'Error al guardar');
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#d33' });
            },
            complete: function() {
                $('#btn-guardar-desparasitacion').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    // ============================================================
    // 🔥 AUTOCOMPLETAR AL CAMBIAR PRODUCTO
    // ============================================================
    $('#select-desparasitante').on('change', function() {
        let tipo = $(this).find('option:selected').data('tipo');
        let laboratorio = $(this).find('option:selected').data('laboratorio');
        $('select[name="tipo"]').val(tipo);
        $('input[name="laboratorio"]').val(laboratorio);
    });

});

// ============================================================
// 🔥 FUNCIONES GLOBALES (FUERA DEL DOCUMENT.READY)
// ============================================================

function verDesparasitacion(id) {
    $.ajax({
        url: '/desparasitaciones/' + id + '/detalle',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let a = response.aplicacion;
                $('#ver-desp-mascota').text(a.mascota || 'Mascota');
                let inicial = a.mascota ? a.mascota.charAt(0).toUpperCase() : 'M';
                $('#ver-desp-iniciales').text(inicial);
                $('#ver-desp-cliente').text(a.cliente || 'Sin dueño');
                
                let badge = $('#ver-desp-estado');
                badge.text((a.estado || 'aplicada').toUpperCase());
                if (a.estado === 'aplicada') badge.css('background-color', '#22c55e');
                else if (a.estado === 'programada') badge.css('background-color', '#f59e0b');
                else badge.css('background-color', '#ef4444');

                $('#ver-desp-dueño').text(a.cliente || '--');
                $('#ver-desp-especie').text((a.mascota_especie || '') + ' / ' + (a.mascota_raza || ''));
                $('#ver-desp-veterinario').text(a.veterinario ? 'Dr. ' + a.veterinario : 'No asignado');
                $('#ver-desp-producto').text(a.producto || '--');
                $('#ver-desp-fecha').text(a.fecha_aplicacion || '--');
                $('#ver-desp-proxima').text(a.fecha_proxima || 'No programada');

                $('#modal-ver-desparasitacion').modal('show');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
        }
    });
}

function editarDesparasitacion(id) {
    $.ajax({
        url: '/desparasitaciones/' + id + '/detalle',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let a = response.aplicacion;
                
                $('#edit_desp_id').val(a.id);
                $('#edit_desp_mascota').val(a.mascota);
                $('#edit_desp_producto').val(a.producto);
                
                // IDs ocultos
                $('#edit_desp_mascota_id').val(a.mascota_id); 
                $('#edit_desp_usuario_id').val(a.veterinario_id);
                $('#edit_desp_desparasitante_id').val(a.desparasitante_id);
                
                // 🔥 AQUÍ SE CARGAN LAS FECHAS
                $('#edit_desp_fecha_aplicacion').val(a.fecha_aplicacion_raw || '');
                $('#edit_desp_fecha_proxima').val(a.fecha_proxima_raw || '');
                
                $('#edit_desp_estado').val(a.estado);
                $('#edit_desp_lote').val(a.lote);
                $('#edit_desp_dosis').val(a.dosis);
                $('#edit_desp_costo').val(a.costo);
                $('#edit_desp_observaciones').val(a.observaciones);
                
                $('#modal-editar-desparasitacion').modal('show');
            }
        }
    });
}

function eliminarDesparasitacion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Eliminar este registro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '/desparasitaciones/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire({ icon: 'success', title: 'Eliminado', text: response.message, timer: 1500, showConfirmButton: false });
                if (window.tablaDesparasitaciones) window.tablaDesparasitaciones.ajax.reload(null, false);
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar', confirmButtonColor: '#d33' });
            }
        });
    });
}



// Guardar edición
// Guardar edición (Blindado contra doble ejecución)
$(document).off('click', '#btn-actualizar-desparasitacion').on('click', '#btn-actualizar-desparasitacion', function() {
    let id = $('#edit_desp_id').val();
    let formData = $('#form-editar-desparasitacion').serialize();
    
    $.ajax({
        url: '/desparasitaciones/' + id,
        method: 'PUT',
        data: formData,
        headers: { 
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        beforeSend: function() {
            $('#btn-actualizar-desparasitacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        },
        success: function(response) {
            if (response.success) {
                $('#modal-editar-desparasitacion').modal('hide');
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Actualizado', 
                    text: response.message, 
                    timer: 1500, 
                    showConfirmButton: false 
                });
                if (window.tablaDesparasitaciones) window.tablaDesparasitaciones.ajax.reload(null, false);
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON?.errors;
            let msg = errors ? Object.values(errors).flat().join('\n') : 'Error al actualizar';
            Swal.fire('Error', msg, 'error');
        },
        complete: function() {
            $('#btn-actualizar-desparasitacion').prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar');
        }
    });
});
</script>
@stop