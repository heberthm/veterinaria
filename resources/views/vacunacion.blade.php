@extends('layouts.app')

@section('titulo', 'Vacunación')

{{-- 🔥 AGREGAR ESTO PARA CARGAR EL CSS DE SELECT2 --}}
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    /* Ajuste para que el Select2 se vea como un form-control de Bootstrap */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    /* Asegurar que el dropdown esté por encima del modal */
    .select2-container--open {
        z-index: 9999 !important;
    }
</style>
@endpush


@php
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
                        {{-- 🔥 AQUÍ ESTÁ LA CLAVE: la tabla NO debe tener datos previos ni duplicados --}}
                        <table  id="tabla-vacunas" class="table table-hover table-striped" style="width:100%; font-size:12.5px;" >
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mascota</th>
                                    <th>Propietario</th>
                                    <th>Vacuna</th>
                                    <th>Fecha Aplicación</th>
                                    <th>Veterinario</th>
                                    <th>Próxima dosis</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody> {{-- 🔥 IMPORTANTE: cuerpo vacío --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL NUEVA VACUNA --}}
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
               
                
                <form id="form-vacuna" action="{{ route('vacunas.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota <span class="text-danger">*</span></label>
                                <select name="mascota_id" id="select-mascota" class="form-control select2" required style="width:100%">
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
                                    <label>Nombre de la Vacuna <span class="text-danger">*</span></label>
                                    <select name="vacuna_id" id="select-vacuna" class="form-control" required>
                                        <option value="">Seleccionar vacuna...</option>
                                        @foreach($vacunas as $vacuna)
                                            <!-- 🔥 AGREGAR EL DATA-LABORATORIO AQUÍ -->
                                            <option value="{{ $vacuna->id }}" data-laboratorio="{{ $vacuna->laboratorio }}">
                                                {{ $vacuna->nombre }} (Stock: {{ $vacuna->stock }})
                                            </option>
                                        @endforeach
                                    </select>
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
                               <input type="number" name="dosis_aplicada" class="form-control" step="0.01" min="0" placeholder="Ej: 1.00">
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

{{-- MODAL VER DETALLE ESTILIZADO --}}
<div class="modal fade" id="modal-ver-vacuna" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            
            <!-- Header Oscuro con Icono -->
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle de Vacunación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body" style="padding: 25px; background-color: #ffffff;">
                
                <!-- Encabezado del Perfil (Avatar + Nombre) -->
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                         style="width: 65px; height: 65px; background-color: #3b82f6; font-size: 24px; font-weight: bold; margin-right: 15px;">
                        <!-- Iniciales de la mascota -->
                        <span id="detalle-iniciales">M</span>
                    </div>
                    <div>
                        <h4 id="detalle-mascota" style="margin: 0; font-weight: 700; color: #1e293b; font-size: 22px;">Nombre Mascota</h4>
                        <div class="d-flex align-items-center mt-1">
                            <span id="detalle-cliente" style="color: #64748b; font-size: 14px; margin-right: 10px;">Dueño</span>
                            <span id="detalle-estado" class="badge" style="background-color: #22c55e; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px;">Aplicada</span>
                        </div>
                    </div>
                </div>

                <!-- Columnas de Información -->
                <div class="row">
                    <!-- Columna Izquierda: Datos de la Mascota/Cliente -->
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #1e293b; font-weight: 700; font-size: 15px;">
                            <i class="fas fa-paw" style="color: #64748b;"></i> Mascota y Dueño
                        </h6>
                        
                        <!-- Fila: Propietario -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-user"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Propietario:</strong> <span id="detalle-dueño">--</span>
                            </div>
                        </div>

                       <!-- Fila: Especie -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-dog"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Especie:</strong> <span id="detalle-especie">--</span>
                            </div>
                        </div>

                        <!-- Fila: Raza -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-paw"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Raza:</strong> <span id="detalle-raza">--</span>
                            </div>
                        </div>

                        <!-- Fila: Veterinario -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-user-md"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Veterinario:</strong> <span id="detalle-veterinario">--</span>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Datos de la Vacuna -->
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #1e293b; font-weight: 700; font-size: 15px;">
                            <i class="fas fa-syringe" style="color: #64748b;"></i> Información de la Vacuna
                        </h6>

                        <!-- Fila: Vacuna -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-vial"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Vacuna:</strong> <span id="detalle-producto">--</span>
                            </div>
                        </div>

                        <!-- Fila: Fecha Aplicación -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-check"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Aplicación:</strong> <span id="detalle-fecha">--</span>
                            </div>
                        </div>

                        <!-- Fila: Próxima Dosis -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-alt"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Próxima Dosis:</strong> <span id="detalle-proxima">--</span>
                            </div>
                        </div>

                        <!-- Fila: Lote -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-barcode"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Lote:</strong> <span id="detalle-lote">--</span>
                            </div>
                        </div>
                        
                        <!-- Fila: Costo -->
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-dollar-sign"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Costo:</strong> <span id="detalle-costo">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="modal-editar-vacuna" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Vacunación</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-editar-vacuna">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="via_aplicacion" value="subcutanea">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota</label>
                                <input type="text" id="edit_mascota" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vacuna</label>
                                <input type="text" id="edit_vacuna" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Aplicación</label>
                                <input type="date" name="fecha_aplicacion" id="edit_fecha_aplicacion" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Próxima</label>
                                <input type="date" name="fecha_proxima" id="edit_fecha_proxima" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="estado" id="edit_estado" class="form-control" required>
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
                                <label>Lote</label>
                                <input type="text" name="lote" id="edit_lote" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dosis</label>
                                <input type="number" name="dosis_aplicada" id="edit_dosis" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Costo</label>
                                <input type="number" name="costo" id="edit_costo" class="form-control" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btn-actualizar-vacuna"><i class="fas fa-save"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL PARA VER DETALLE DE VACUNA                            --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-ver-vacuna" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle de Vacunación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; background-color: #ffffff;">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                         style="width: 65px; height: 65px; background-color: #3b82f6; font-size: 24px; font-weight: bold; margin-right: 15px;">
                        <span id="detalle-iniciales">M</span>
                    </div>
                    <div>
                        <h4 id="detalle-mascota" style="margin: 0; font-weight: 700; color: #1e293b; font-size: 22px;">Nombre Mascota</h4>
                        <div class="d-flex align-items-center mt-1">
                            <span id="detalle-cliente" style="color: #64748b; font-size: 14px; margin-right: 10px;">Dueño</span>
                            <span id="detalle-estado" class="badge" style="background-color: #22c55e; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px;">Aplicada</span>
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
                                <strong>Propietario:</strong> <span id="detalle-dueño">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-dog"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Especie/Raza:</strong> <span id="detalle-especie">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-user-md"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Veterinario:</strong> <span id="detalle-veterinario">--</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3" style="color: #1e293b; font-weight: 700; font-size: 15px;">
                            <i class="fas fa-syringe" style="color: #64748b;"></i> Información de la Vacuna
                        </h6>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-vial"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Vacuna:</strong> <span id="detalle-producto">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-check"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Aplicación:</strong> <span id="detalle-fecha">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-calendar-alt"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Próxima Dosis:</strong> <span id="detalle-proxima">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-barcode"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Lote:</strong> <span id="detalle-lote">--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2 p-2" style="background-color: #f8fafc; border-radius: 8px;">
                            <div class="mr-3 text-center" style="width: 25px; color: #3b82f6;"><i class="fas fa-dollar-sign"></i></div>
                            <div style="font-size: 13px; color: #1e293b;">
                                <strong>Costo:</strong> <span id="detalle-costo">--</span>
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

{{-- ============================================================ --}}
{{-- MODAL PARA EDITAR VACUNA                                     --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-editar-vacuna" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Vacunación</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-editar-vacuna">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mascota</label>
                                <input type="text" id="edit_mascota" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vacuna</label>
                                <input type="text" id="edit_vacuna" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Aplicación</label>
                                <input type="date" name="fecha_aplicacion" id="edit_fecha_aplicacion" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Próxima</label>
                                <input type="date" name="fecha_proxima" id="edit_fecha_proxima" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="estado" id="edit_estado" class="form-control" required>
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
                                <label>Lote</label>
                                <input type="text" name="lote" id="edit_lote" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dosis</label>
                                <input type="number" name="dosis_aplicada" id="edit_dosis" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Costo</label>
                                <input type="number" name="costo" id="edit_costo" class="form-control" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btn-actualizar-vacuna"><i class="fas fa-save"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// 🔥 FLAG GLOBAL PARA EVITAR DOBLE INICIALIZACIÓN
if (typeof window.vacunacionInicializada === 'undefined') {
    window.vacunacionInicializada = false;
}

$(document).ready(function() {
    // 🔥 SI YA SE INICIALIZÓ, NO VOLVER A HACERLO
    if (window.vacunacionInicializada) {
        console.warn('[Vacunación] DataTable ya inicializada, se omite.');
        return;
    }
    window.vacunacionInicializada = true;

    // 🔥 DESTRUIR INSTANCIA PREVIA DE DATATABLE SI EXISTE
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabla-vacunas')) {
        $('#tabla-vacunas').DataTable().destroy();
    }

   
     // ============================================================
    // 🔥 SELECT2 PARA MASCOTA
    // ============================================================
    if ($.fn.select2 && $('#select-mascota').length && !$('#select-mascota').hasClass('select2-hidden-accessible')) {
        $('#select-mascota').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar mascota...',
            allowClear: true,
            minimumInputLength: 1, 
            dropdownParent: $('#modal-vacuna'), // 🔥 Asegurar que es el modal correcto
            language: {
                inputTooShort: function() { return 'Escribe para buscar...'; },
                noResults: function() { return 'No se encontraron mascotas'; }
            }
        });

        // 🔥 FORZAR EL FOCO AL ABRIR (Sin cerrar el modal)
        $('#select-mascota').on('select2:open', function () {
            setTimeout(function () {
                let searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            }, 50);
        });
    }

        // ============================================================
    // 🔥 SELECT2 PARA VETERINARIO (Carga local, sin AJAX)
    // ============================================================
    if ($.fn.select2 && $('#select-veterinario').length && !$('#select-veterinario').hasClass('select2-hidden-accessible')) {
        $('#select-veterinario').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar veterinario...',
            allowClear: true,
            dropdownParent: $('#modal-vacuna'),
            language: {
                noResults: function() { return 'No se encontraron veterinarios'; },
                searching: function() { return 'Buscando...'; }
            }
        });

        // 🔥 FORZAR EL FOCO AL HACER CLIC
        $('#select-veterinario').on('select2:open', function () {
            setTimeout(function () {
                let searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) searchField.focus();
            }, 50);
        });
    }

    // ============================================================
    // 🔥 INICIALIZAR DATATABLE
    // ============================================================
    window.tablaVacunas = $('#tabla-vacunas').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('vacunas.datatable') }}",
        columns: [
            { data: 'id', name: 'a.id', className: 'text-center', width: '50px' },
            { data: 'mascota_nombre', name: 'm.nombre' },
            { data: 'cliente_nombre', name: 'cliente_nombre' },
            { data: 'producto', name: 'p.nombre' },
            { data: 'fecha_aplicacion_formato', name: 'a.fecha_aplicacion', className: 'text-center' },
            { data: 'veterinario_nombre', name: 'u.name' },
            { data: 'fecha_proxima_formato', name: 'a.fecha_proxima', className: 'text-center' },
            { data: 'estado_badge', name: 'a.estado', className: 'text-center' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            processing:     'Procesando...',
            search:         'Buscar:',
            lengthMenu:     'Mostrar _MENU_ registros',
            info:           'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty:      'Mostrando 0 a 0 de 0 registros',
            infoFiltered:   '(filtrado de _MAX_ registros totales)',
            loadingRecords: 'Cargando...',
            zeroRecords:    'No se encontraron resultados',
            emptyTable:     'No hay datos disponibles',
            paginate: {
                first:    'Primero',
                previous: 'Anterior',
                next:     'Siguiente',
                last:     'Último'
            }
        }
    });

    // ============================================================
    // 🔥 GUARDAR VACUNA
    // ============================================================
    $('#btn-guardar-vacuna').off('click').on('click', function() {
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
                        title: '¡Guardado!',
                        text: response.message || 'Vacuna guardada correctamente',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });

                    if (window.tablaVacunas) {
                        window.tablaVacunas.ajax.reload(null, false);
                    }

                    $('#form-vacuna')[0].reset();
                    if ($('#select-mascota').hasClass('select2-hidden-accessible')) {
                        $('#select-mascota').val('').trigger('change');
                    }
                    if ($('#select-veterinario').hasClass('select2-hidden-accessible')) {
                        $('#select-veterinario').val('').trigger('change');
                    }
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

        // ============================================================
    // 🔥 AUTOCOMPLETAR LABORATORIO AL SELECCIONAR VACUNA
    // ============================================================
    $('#select-vacuna').on('change', function() {
        // Obtener el atributo data-laboratorio de la opción seleccionada
        let laboratorio = $(this).find('option:selected').data('laboratorio');
        
        // Si tiene laboratorio, lo ponemos en el input, si no, lo dejamos vacío
        $('input[name="laboratorio"]').val(laboratorio || '');
    });

        $('#modal-vacuna').off('hidden.bs.modal').on('hidden.bs.modal', function() {
        $('#form-vacuna')[0].reset(); // El reset ya limpia el input de laboratorio
        
        // Limpiar Select2 de mascota
        if ($('#select-mascota').hasClass('select2-hidden-accessible')) {
            $('#select-mascota').val('').trigger('change');
        }
        // Limpiar Select2 de veterinario
        if ($('#select-veterinario').hasClass('select2-hidden-accessible')) {
            $('#select-veterinario').val('').trigger('change');
        }
        // 🔥 Limpiar Select2 de vacuna
        if ($('#select-vacuna').hasClass('select2-hidden-accessible')) {
            $('#select-vacuna').val('').trigger('change');
        }
        
        $('#btn-guardar-vacuna').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
    });
});

 // ============================================================
    // 🔥 FUNCIONES GLOBALES (FUERA DEL DOCUMENT.READY)
    // ============================================================

    function verVacuna(id) {
        $.ajax({
            url: '/vacunas/' + id + '/detalle',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let v = response.vacuna;
                    
                    $('#detalle-mascota').text(v.mascota_nombre || 'Mascota');
                    let inicial = v.mascota_nombre ? v.mascota_nombre.charAt(0).toUpperCase() : 'M';
                    $('#detalle-iniciales').text(inicial);
                    $('#detalle-cliente').text(v.cliente_nombre || 'Sin dueño');
                    
                    let estadoBadge = $('#detalle-estado');
                    estadoBadge.text((v.estado || 'aplicada').toUpperCase());
                    if (v.estado === 'aplicada') estadoBadge.css('background-color', '#22c55e');
                    else if (v.estado === 'programada') estadoBadge.css('background-color', '#f59e0b');
                    else estadoBadge.css('background-color', '#ef4444');

                    $('#detalle-dueño').text(v.cliente_nombre || '--');
                    $('#detalle-especie').text(v.mascota_especie || '--');
                    $('#detalle-raza').text(v.mascota_raza || '--');
                    $('#detalle-veterinario').text(v.veterinario_nombre ? 'Dr. ' + v.veterinario_nombre : 'No asignado');
                    $('#detalle-producto').text(v.producto || '--');
                    $('#detalle-fecha').text(v.fecha_aplicacion || '--');
                    $('#detalle-proxima').text(v.fecha_proxima || 'No programada');
                    $('#detalle-lote').text(v.lote || 'No registrado');
                    
                    let costoFormateado = parseFloat(v.costo || 0).toLocaleString('es-CO');
                    $('#detalle-costo').text('$' + costoFormateado);

                    $('#modal-ver-vacuna').modal('show');
                }
            },
            error: function(xhr) {
                // 🔥 Muestra el error real en consola
                console.error(xhr.responseJSON ? xhr.responseJSON.message : 'Error de conexión');
                Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'No se pudo cargar el detalle', 'error');
            }
        });
       
    }

    function editarVacuna(id) {
        $.ajax({
            url: '/vacunas/' + id + '/detalle',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let v = response.vacuna;
                    $('#edit_id').val(v.id);
                    $('#edit_mascota').val(v.mascota_nombre);
                    $('#edit_vacuna').val(v.producto);
                    $('#edit_fecha_aplicacion').val(v.fecha_aplicacion);
                    $('#edit_fecha_proxima').val(v.fecha_proxima);
                    $('#edit_estado').val(v.estado);
                    $('#edit_lote').val(v.lote);
                    $('#edit_dosis').val(v.dosis_aplicada);
                    $('#edit_costo').val(v.costo);
                    $('#edit_observaciones').val(v.observaciones);
                    
                    $('#modal-editar-vacuna').modal('show');
                }
            }
        });
    }

    function eliminarVacuna(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Eliminar este registro de vacunación? El stock vuelve al inventario.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '/vacunas/' + id,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire({ icon: 'success', title: 'Eliminado', text: response.message || 'Registro eliminado', confirmButtonColor: '#28a745' });
                    if (window.tablaVacunas) {
                        window.tablaVacunas.ajax.reload(null, false);
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el registro', confirmButtonColor: '#d33' });
                }
            });
        });
    }

    // 🔥 FUNCIÓN PARA GUARDAR LA EDICIÓN
    $(document).on('click', '#btn-actualizar-vacuna', function() {
        let id = $('#edit_id').val();
        let formData = $('#form-editar-vacuna').serialize();
        
        $.ajax({
            url: '/vacunas/' + id, 
            method: 'POST', 
            data: formData + '&_method=PUT',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend: function() {
                $('#btn-actualizar-vacuna').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
            },
            success: function(response) {
                if (response.success) {
                    $('#modal-editar-vacuna').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Actualizado', text: response.message, timer: 1500, showConfirmButton: false });
                    if (window.tablaVacunas) window.tablaVacunas.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                let msg = errors ? Object.values(errors).flat().join('\n') : 'Error al actualizar';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $('#btn-actualizar-vacuna').prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar');
            }
        });
    });

    // 🔥 FORZAR SCOPE GLOBAL (Aunque estén fuera del ready, esto es una capa extra de seguridad)
    window.verVacuna = verVacuna;
    window.editarVacuna = editarVacuna;
    window.eliminarVacuna = eliminarVacuna;


</script>
@stop