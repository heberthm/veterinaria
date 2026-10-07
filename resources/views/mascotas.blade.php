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
                        <table id="tabla-mascotas" class="table table-hover table-striped" style="width:100%; font-size:12.5px;">
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
                        </table>´
                      </div>   
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
                                <select name="cliente_id" id="cliente_id_select" 
                                        class="select2-clientes" required style="width:100%">
                                    <option value="">Buscar propietario...</option>
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
                                    <option value="otro">otro</option>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Género <span class="text-danger">*</span></label>
                            <select name="genero" class="form-control" required>
                                <option value="">Seleccionar...</option>
                                <option value="macho">♂️ Macho</option>
                                <option value="hembra">♀️ Hembra</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento_mascota"
                                class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Edad</label>
                           <input type="text" id="edad_display"
                                class="form-control" readonly
                                style="background:#f8f9fa;font-weight:600;color:#28a745;"
                                placeholder="—">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Peso (kg)</label>
                            <input type="number" name="peso" class="form-control"
                                step="0.01" min="0" max="200" placeholder="Ej: 5.5">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número de Chip</label>
                            <input type="text" name="numero_chip" class="form-control"
                                placeholder="978101082776321">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-control" required>
                                <option value="activo">🟢 Activo</option>
                                <option value="inactivo">⚪ Inactivo</option>
                                <option value="fallecido">⚫ Fallecido</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Esterilizado</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" name="esterilizado" class="custom-control-input"
                                    id="esterilizado-switch">
                                <label class="custom-control-label" for="esterilizado-switch">
                                   No / Si
                                </label>
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
<div class="modal fade" id="modalDetalleMascota" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:14px;border:none;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-paw"></i> Detalle de la Mascota
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalleMascotaBody">
                {{-- vacío, el JS lo llena --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- MODAL FLTRAR HISTORIA CLINICA POR FECHA --}}
{{-- ============================================================ --}}
    <div class="modal fade" id="modalHistoria" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" id="formHistoria">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">Historia clínica · <span id="histMascotaNombre"></span></h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <input type="hidden" id="histId">

            <div class="form-group">
            <label>Consulta</label>
            <select id="histSelector" class="form-control"></select>
            <small class="text-muted">Veterinario: <span id="histVeterinario">—</span></small>
            </div>

            <div class="form-row">
            <div class="form-group col-md-6">
                <label>Fecha de consulta</label>
                <input type="datetime-local" name="fecha_consulta" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label>Próxima cita sugerida</label>
                <input type="date" name="proxima_cita_sugerida" class="form-control">
            </div>
            </div>

            <div class="form-group">
            <label>Motivo de consulta</label>
            <input type="text" name="motivo_consulta" class="form-control">
            </div>
            <div class="form-group">
            <label>Anamnesis</label>
            <textarea name="anamnesis" rows="2" class="form-control"></textarea>
            </div>

            <h6 class="mt-3">Signos vitales</h6>
            <div class="form-row">
            <div class="form-group col-md-3"><label>Temp. (°C)</label><input type="number" step="0.1" name="temperatura" class="form-control"></div>
            <div class="form-group col-md-3"><label>F. cardiaca</label><input type="number" name="frecuencia_cardiaca" class="form-control"></div>
            <div class="form-group col-md-3"><label>F. respiratoria</label><input type="number" name="frecuencia_respiratoria" class="form-control"></div>
            <div class="form-group col-md-3"><label>Peso (kg)</label><input type="number" step="0.01" name="peso" class="form-control"></div>
            </div>
            <div class="form-row">
            <div class="form-group col-md-6"><label>Estado corporal</label><input type="text" name="estado_corporal" class="form-control"></div>
            <div class="form-group col-md-6"><label>Hidratación</label><input type="text" name="hidratacion" class="form-control"></div>
            </div>

            <div class="form-group"><label>Examen físico</label><textarea name="examen_fisico" rows="2" class="form-control"></textarea></div>
            <div class="form-group"><label>Diagnóstico</label><textarea name="diagnostico" rows="2" class="form-control"></textarea></div>
            <div class="form-group"><label>Tratamiento</label><textarea name="tratamiento" rows="3" class="form-control"></textarea></div>
            <div class="form-group"><label>Observaciones</label><textarea name="observaciones" rows="2" class="form-control"></textarea></div>

            <hr>
            <label class="mb-2">Archivos adjuntos</label>
            <div id="histArchivos" class="d-flex flex-wrap" style="gap:10px"></div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
        </form>
    </div>
    </div>


@stop

@push('css')
<style>
 .btn-accion {
    display:inline-flex; align-items:center; justify-content:center;
    width:24px; height:24px; border-radius:5px; border:none;
    color:#fff; font-size:10px; cursor:pointer; margin:0 1px;
    padding:0; line-height:1;
    transition: transform .1s, filter .15s;
    }
    .btn-accion:hover  { filter: brightness(1.1); }
    .btn-accion:active { transform: scale(.95); }
    .btn-accion i      { font-size:10px; }

    .btn-accion-ver      { background:#007bff; }
    .btn-accion-editar   { background:#ffc107; color:#212529; }
    .btn-accion-eliminar { background:#dc3545; }

    /* 🔥 Contenedor de acciones */
    .col-acciones {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;                   /* 👈 separación adicional entre botones */
        white-space: nowrap;
    }

    /* Evita que se partan en varias líneas */
    #tabla-mascotas td:last-child {
        white-space: nowrap;
        text-align: center;
        width: 110px;
    }

    .cd-avatar {
    width:56px; height:56px; border-radius:50%;
    background:#28a745;              /* 👈 verde */
    color:#ffffff;                   /* 👈 letra blanca */
    font-weight:700; font-size:22px;
    display:flex; align-items:center; justify-content:center;
    text-transform:uppercase;
    }

    .cd-item {
    display:flex; align-items:center; gap:10px;
    background:#f8f9fa; padding:10px 14px; border-radius:8px;
    font-size:13px; color:#495057; margin-bottom:6px;
    }
    .cd-item i { color:#007bff; width:16px; }

    .cd-pet {
        display:flex; align-items:center; gap:10px;
        background:#ede7f6; padding:10px 14px; border-radius:8px;
        font-size:13px; color:#5e35b1; margin-bottom:6px;
    }

    .cd-avatar {
        width:56px; height:56px; border-radius:50%;
        background:#28a745;
        color:#fff; font-weight:700; font-size:22px;
        display:flex; align-items:center; justify-content:center;
        text-transform:uppercase;
    }

    .badge-activo   { background:#28a745; color:#fff; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; }
    .badge-inactivo { background:#dc3545; color:#fff; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; }

    /* Dropdown por encima de todo */
.select2-container {
    z-index: 9999 !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

/* El modal debe permitir que el dropdown se salga de su caja */
#modal-mascota .modal-content {
    overflow: visible !important;
}

#modal-mascota .modal-body {
    overflow: visible !important;
}

/* ============================================================ */
/* 🔥 FIX DEFINITIVO: Select2 misma altura que .form-control     */
/* ============================================================ */
    body .select2-container .select2-selection--single,
    body .select2-container--bootstrap4 .select2-selection--single,
    body #modal-mascota .select2-container .select2-selection--single,
    body #modal-mascota .select2-container--bootstrap4 .select2-selection--single {
        height: 37.5px !important;
        min-height: 37.5px !important;
        max-height: 37.5px !important;
        padding: 0 .75rem !important;
        display: flex !important;
        align-items: center !important;
        box-sizing: border-box !important;
        border: 1px solid #ced4da !important;
        border-radius: .25rem !important;
        background-color: #fff !important;
        line-height: normal !important;
    }

    body .select2-container .select2-selection--single .select2-selection__rendered,
    body .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 35.5px !important;
        padding: 0 !important;
        margin: 0 !important;
        color: #495057 !important;
        font-size: 1rem !important;
      
    }

    body .select2-container .select2-selection--single .select2-selection__arrow,
    body .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        min-height: 35.5px !important;
        top: 0 !important;
        right: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    body .select2-container .select2-selection--single .select2-selection__arrow b {
        margin-top: 0 !important;
        border-color: #6c757d transparent transparent transparent !important;
    }

    body .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
        line-height: 35.5px !important;
    }

    body .select2-container .select2-selection--single .select2-selection__clear {
        color: #6c757d !important;
        font-weight: 700 !important;
        margin-right: 6px !important;
        line-height: 35.5px !important;
    }

    .btn-accion.btn-accion-historia {
        background: #16A34A !important;
        color: #fff !important;
        border: 1px solid #16A34A !important;
    }
    .btn-accion.btn-accion-historia:hover {
        background: #15803D !important;
        border-color: #15803D !important;
    }
     
     .btn-accion.btn-historia,
        table.dataTable .btn-accion.btn-historia {
            background: #7C3AED !important;
            color: #fff !important;
            border: 1px solid #7C3AED !important;
        }
        .btn-accion.btn-historia:hover,
        table.dataTable .btn-accion.btn-historia:hover {
            background: #6D28D9 !important;
            border-color: #6D28D9 !important;
            color: #fff !important;
        }
        .btn-accion.btn-historia i { color: #fff !important; }

</style>

@endpush
@push('js')
<script>
$(document).ready(function () {
 
    // ============================================================
// 🔥 SELECT2 CON BÚSQUEDA AJAX — PROPIETARIOS
// ============================================================
if ($.fn.select2) {
    $('#cliente_id_select').select2({
        
        width: '100%',
         dropdownParent: $('#modal-mascota'),
        placeholder: 'Buscar por nombre, documento o celular...',
        allowClear: true,
        minimumInputLength: 2,              // 👈 busca desde 2 caracteres
      
        ajax: {
            url: "{{ route('clientes.buscar') }}",
            dataType: 'json',
            delay: 300,                      // 👈 espera 300ms antes de buscar
            data: function (params) {
                return {
                    q: params.term,          // 👈 término de búsqueda
                    page: params.page || 1,
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.results,   // 👈 array { id, text }
                    pagination: {
                        more: false,         // o true si implementas paginación
                    },
                };
            },
            cache: true,
        },
        language: {
            inputTooShort: function () {
                return 'Escribe al menos 2 caracteres...';
            },
            noResults: function () {
                return 'No se encontraron propietarios';
            },
            searching: function () {
                return 'Buscando...';
            },
            errorLoading: function () {
                return 'Error al cargar resultados';
            },
        },
        templateResult: function (cliente) {
            if (cliente.loading) return cliente.text;

            // 🔥 Personalizar cómo se ve cada opción en el dropdown
            return $(
                `<div>
                    <i class="fas fa-user text-primary mr-2"></i>
                    <strong>${cliente.text}</strong>
                </div>`
            );
        },
        templateSelection: function (cliente) {
            // 🔥 Cómo se ve la opción seleccionada
            if (!cliente.id) return cliente.text;
            return cliente.text;
        },
    });
}

// ============================================================
// 🔥 AUTO-FOCUS EN SELECT2 AL HACER CLIC
// ============================================================
$(document).on('select2:open', function (e) {
    // Enfocar el input de búsqueda del dropdown abierto
    setTimeout(function () {
        const searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.focus();
        }
    }, 50);
});


    // ============================================================
    // DATATABLE
    // ============================================================
    window.tablaMascotas = $('#tabla-mascotas').DataTable({
        retrieve: true,
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
    // GUARDAR MASCOTA (crear o editar)
    // ============================================================
    $('#btn-guardar-mascota').on('click', function () {
        const $form = $('#form-mascota');
        const modo  = $form.data('modo') || 'crear';
        const id    = $form.data('id');
        const esEdicion = modo === 'editar' && id;

        const url = esEdicion ? `/mascotas/${id}` : $form.attr('action');

        let formData = $form.serializeArray();
        if (esEdicion) {
            formData.push({ name: '_method', value: 'PUT' });
        }
         formData.push({ name: 'esterilizado', value: $form.find('[name="esterilizado"]').is(':checked') ? 1 : 0 });

        $.ajax({
            url: url,
            method: 'POST',
            data: $.param(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            },
            beforeSend: function () {
                $('#btn-guardar-mascota').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            },
            success: function (response) {
                if (response.success) {
                    $('#modal-mascota').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: esEdicion ? 'Mascota actualizada' : 'Mascota creada',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.tablaMascotas.ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors;
                let mensaje = 'Error al guardar la mascota';
                if (errors) mensaje = Object.values(errors).flat().join('<br>');
                else if (xhr.responseJSON?.message) mensaje = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', html: mensaje, confirmButtonColor: '#d33' });
            },
            complete: function () {
                $('#btn-guardar-mascota').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    // ============================================================
    // LIMPIAR MODAL AL CERRAR
    // ============================================================
    $('#modal-mascota').on('hidden.bs.modal', function () {
        const $form = $('#form-mascota');
        $form[0].reset();
        $form.find('select').val('').trigger('change');
        $form.removeData('modo').removeData('id');
        
        $('#modal-mascota .modal-title').html('<i class="fas fa-paw text-primary"></i> Nueva Mascota');
        $('#btn-guardar-mascota').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
        $form.find('[name="activo"]').prop('checked', true);
        $form.find('[name="esterilizado"]').prop('checked', false);
        $('#edad_display').val(''); 
    });
});


// ============================================================
// 🔥 FUNCIONES GLOBALES (FUERA del $(document).ready)
// ============================================================

function verMascota(id) {
    const cont = document.getElementById('detalleMascotaBody');
    if (!cont) return;

    fetch(`/mascotas/${id}/detalle`, {
        headers: { 'Accept': 'application/json' }
    })
    .then((res) => {
        if (!res.ok) throw new Error('No se pudo cargar la mascota.');
        return res.json();
    })
    .then((data) => {
        const m = data.mascota || data;

        const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[c]));

        const val   = (v) => (v !== null && v !== undefined && v !== '') ? esc(v) : '—';
        const fecha = (f) => f ? new Date(f).toLocaleDateString('es-CO') : '—';
        const inicial = (m.nombre || '?').charAt(0).toUpperCase();
        const dueno = m.cliente || m.propietario || m.dueno || null;

        cont.innerHTML = `
            <div class="d-flex align-items-center mb-4">
                <div class="cd-avatar mr-3">${esc(inicial)}</div>
                <div>
                    <h5 class="mb-0 font-weight-bold">${val(m.nombre)}</h5>
                    <span class="text-muted small">${val(m.especie)} · ${val(m.raza)}</span>
                    ${m.activo !== undefined ? `
                        <span class="ml-2 ${m.activo ? 'badge-activo' : 'badge-inactivo'}">
                            ${m.activo ? 'Activo' : 'Inactivo'}
                        </span>` : ''}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold"><i class="fas fa-paw"></i> Información</h6>
                    ${m.genero           ? `<div class="cd-item"><i class="fas fa-venus-mars"></i> <strong>Género:</strong> ${val(m.genero)}</div>` : ''}
                    ${m.fecha_nacimiento ? `<div class="cd-item"><i class="fas fa-birthday-cake"></i> <strong>Nacimiento:</strong> ${fecha(m.fecha_nacimiento)}</div>` : ''}
                    ${m.peso             ? `<div class="cd-item"><i class="fas fa-weight"></i> <strong>Peso:</strong> ${esc(m.peso)} kg</div>` : ''}
                    ${m.color            ? `<div class="cd-item"><i class="fas fa-palette"></i> <strong>Color:</strong> ${val(m.color)}</div>` : ''}
                    ${m.numero_chip      ? `<div class="cd-item"><i class="fas fa-tag"></i> <strong>Chip:</strong> ${val(m.numero_chip)}</div>` : ''}
                    ${m.esterilizado !== undefined ? `<div class="cd-item"><i class="fas fa-dog"></i> <strong>Esterilizado:</strong> ${m.esterilizado ? 'Sí' : 'No'}</div>` : ''}
                </div>

                <div class="col-md-6">
                    <h6 class="font-weight-bold"><i class="fas fa-user"></i> Dueño</h6>
                    ${dueno ? `
                        <div class="cd-item"><i class="fas fa-user"></i> <strong>${esc(dueno.nombres || '')} ${esc(dueno.apellidos || '')}</strong></div>
                        ${dueno.celular ? `<div class="cd-item"><i class="fas fa-phone"></i> <strong>Celular:</strong> ${esc(dueno.celular)}</div>` : ''}
                        ${dueno.email   ? `<div class="cd-item"><i class="fas fa-envelope"></i> <strong>Email:</strong> ${esc(dueno.email)}</div>` : ''}
                    ` : '<div class="cd-item text-muted">Sin dueño asignado</div>'}
                </div>
            </div>

            ${(m.notas || m.alergias || m.enfermedades_cronicas) ? `
                <div class="mt-3">
                    <h6 class="font-weight-bold"><i class="fas fa-sticky-note"></i> Notas</h6>
                    ${m.notas ? `<div class="cd-item"><i class="fas fa-comment"></i> ${esc(m.notas)}</div>` : ''}
                    ${m.alergias ? `<div class="cd-item"><i class="fas fa-exclamation-triangle"></i> <strong>Alergias:</strong> ${esc(m.alergias)}</div>` : ''}
                    ${m.enfermedades_cronicas ? `<div class="cd-item"><i class="fas fa-heartbeat"></i> <strong>Crónicas:</strong> ${esc(m.enfermedades_cronicas)}</div>` : ''}
                </div>
            ` : ''}
        `;

        jQuery('#modalDetalleMascota').modal('show');
    })
    .catch((err) => {
        cont.innerHTML = `<div class="alert alert-danger mb-0">${esc(err.message)}</div>`;
        jQuery('#modalDetalleMascota').modal('show');
    });
}


function editarMascota(id) {
    fetch(`/mascotas/${id}/edit`, {
        headers: { 'Accept': 'application/json' }
    })
    .then((res) => {
        if (!res.ok) throw new Error('No se pudo cargar la mascota.');
        return res.json();
    })
    .then((data) => {
        const m = data.mascota || data;
        console.log('Datos de la mascota:', m);   // 👈 temporal para debug

         // 🔥 Precargar el propietario actual en el Select2
        if (m.cliente_id) {
            const clienteTexto = m.cliente
                ? `${m.cliente.nombres} ${m.cliente.apellidos} - ${m.cliente.numero_documento}`
                : `Cliente #${m.cliente_id}`;

            // Crear la opción si no existe
            const $select = $('#cliente_id_select');
            if ($select.find("option[value='" + m.cliente_id + "']").length === 0) {
                const option = new Option(clienteTexto, m.cliente_id, true, true);
                $select.append(option).trigger('change');
            } else {
                $select.val(m.cliente_id).trigger('change');
            }
        }


        $('#modal-mascota .modal-title').html('<i class="fas fa-paw text-primary"></i> Editar Mascota');

        $('#form-mascota [name="cliente_id"]').val(m.cliente_id).trigger('change');
        $('#form-mascota [name="nombre"]').val(m.nombre || '');
        $('#form-mascota [name="especie"]').val(m.especie || '');
        $('#form-mascota [name="raza"]').val(m.raza || '');
        $('#form-mascota [name="color"]').val(m.color || '');
        $('#form-mascota [name="genero"]').val(m.genero || '');

        // 🔥 FECHA DE NACIMIENTO
        const fechaNac = m.fecha_nacimiento
            ? String(m.fecha_nacimiento).substring(0, 10)   // "2021-04-14"
            : '';
        $('#form-mascota [name="fecha_nacimiento"]').val(fechaNac).trigger('change');

        // 🔥 ESTADO
        $('#form-mascota [name="estado"]').val(m.estado || 'activo');

        $('#form-mascota [name="peso"]').val(m.peso || '');
        $('#form-mascota [name="numero_chip"]').val(m.numero_chip || '');
        $('#form-mascota [name="esterilizado"]').prop('checked', !!m.esterilizado);
        $('#form-mascota [name="alergias"]').val(m.alergias || '');
        $('#form-mascota [name="enfermedades_cronicas"]').val(m.enfermedades_cronicas || '');
        $('#form-mascota [name="notas"]').val(m.notas || '');

        const $form = $('#form-mascota');
        $form.data('modo', 'editar');
        $form.data('id', m.id);

        $('#modal-mascota').modal('show');
    })
    .catch((err) => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message,
            confirmButtonColor: '#d33'
        });
    });
}


function eliminarMascota(id) {
    Swal.fire({
        title: '¿Eliminar esta mascota?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`/mascotas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            }
        })
        .then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Error al eliminar.');
            }
            return data;
        })
        .then((data) => {
            if (window.tablaMascotas) window.tablaMascotas.ajax.reload(null, false);
            Swal.fire({
                icon: 'success',
                title: 'Mascota eliminada',
                text: data.message || '',
                timer: 1500,
                showConfirmButton: false
            });
        })
        .catch((err) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                confirmButtonColor: '#d33'
            });
        });
    });
}

// ============================================================
// 🔥 CÁLCULO AUTOMÁTICO DE EDAD
// ============================================================
function calcularEdad(fechaStr) {
    if (!fechaStr) return '—';

    const hoy = new Date();
    const nacimiento = new Date(fechaStr);

    if (isNaN(nacimiento.getTime())) return '—';
    if (nacimiento > hoy) return 'Fecha inválida';

    let años = hoy.getFullYear() - nacimiento.getFullYear();
    let meses = hoy.getMonth() - nacimiento.getMonth();
    let dias  = hoy.getDate() - nacimiento.getDate();

    // Ajustar si aún no ha cumplido años/meses este año
    if (dias < 0) {
        meses--;
        const ultimoMes = new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate();
        dias += ultimoMes;
    }
    if (meses < 0) {
        años--;
        meses += 12;
    }

    // Formato de salida
    if (años === 0 && meses === 0) {
        return `${dias} día${dias !== 1 ? 's' : ''}`;
    }
    if (años === 0) {
        return `${meses} mes${meses !== 1 ? 'es' : ''}${dias > 0 ? ` y ${dias} día${dias !== 1 ? 's' : ''}` : ''}`;
    }
    if (meses === 0) {
        return `${años} año${años !== 1 ? 's' : ''}`;
    }
    return `${años} año${años !== 1 ? 's' : ''} y ${meses} mes${meses !== 1 ? 'es' : ''}`;
}

// Escuchar cambios en la fecha de nacimiento
$('#fecha_nacimiento_mascota').on('input change', function () {
    const fecha = $(this).val();
    $('#edad_display').val(calcularEdad(fecha));
});

// ============================================================
// 🔥 FILTRR HISTORIA CLINICA POR FECHA
// ============================================================


    const baseHist = @json(url('mascotas'));
    const camposHist = ['fecha_consulta','proxima_cita_sugerida','motivo_consulta','anamnesis',
        'temperatura','frecuencia_cardiaca','frecuencia_respiratoria','peso',
        'estado_corporal','hidratacion','examen_fisico','diagnostico','tratamiento','observaciones'];

    const escHtml = (s) => $('<div>').text(s ?? '').html();

    function cargarHistoria(id) {
        $.get(`${baseHist}/historia/${id}`)
            .done(res => {
                const h = res.historia;
                $('#histId').val(h.id);
                $('#histVeterinario').text(h.veterinario ?? '—');
                camposHist.forEach(k => $(`#formHistoria [name="${k}"]`).val(h[k] ?? ''));

                const $a = $('#histArchivos').empty();
                if (!res.archivos.length) {
                    $a.html('<p class="text-muted small mb-0">Sin archivos adjuntos.</p>');
                    return;
                }
                res.archivos.forEach(f => {
                    const preview = f.tipo === 'imagen'
                        ? `<img src="${f.url}" alt="" style="width:100%;height:90px;object-fit:cover;border-radius:6px">`
                        : `<div class="d-flex align-items-center justify-content-center bg-light" style="height:90px;border-radius:6px">
                            <i class="far fa-file-pdf fa-2x text-danger"></i></div>`;
                    $a.append(`
                        <a href="${f.url}" target="_blank" rel="noopener" class="text-dark" style="width:120px;text-decoration:none">
                            ${preview}
                            <div class="small text-truncate mt-1" title="${escHtml(f.nombre)}">${escHtml(f.nombre)}</div>
                            <div class="small text-muted">${escHtml(f.peso_kb)} KB</div>
                        </a>`);
                });
            })
            .fail(() => Swal.fire('Error', 'No se pudo cargar la consulta.', 'error'));
    }

    // Botón en la tabla de mascotas
    $(document).on('click', '.btn-historia', function () {
        const mascotaId = $(this).data('id');

        $.get(`${baseHist}/${mascotaId}/historias`)
            .done(res => {
                $('#histMascotaNombre').text(res.mascota.nombre);
                $('#histSelector').html(
                    res.historias.map(h => `<option value="${h.id}">${escHtml(h.etiqueta)}</option>`).join('')
                );
                cargarHistoria(res.historias[0].id);   // la más reciente
                $('#modalHistoria').modal('show');
            })
            .fail(xhr => Swal.fire('Sin historia', xhr.responseJSON?.message ?? 'No se pudo cargar la historia.', 'info'));
    });

    // Cambiar de consulta
    $('#histSelector').on('change', function () { cargarHistoria(this.value); });

    // Guardar
    $('#formHistoria').on('submit', function (e) {
        e.preventDefault();
        const id = $('#histId').val();

        $.ajax({
            url: `${baseHist}/historia/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('#formHistoria input[name=_token]').val() },
        })
        .done(res => {
            toastr.success(res.message);
            // refresca la etiqueta del selector con fecha/motivo nuevos
            const motivo = $('#formHistoria [name=motivo_consulta]').val() || 'Sin motivo';
            const fecha = $('#formHistoria [name=fecha_consulta]').val();
            if (fecha) {
                const d = new Date(fecha);
                const f = d.toLocaleString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
                $('#histSelector option:selected').text(`${f} · ${motivo}`);
            }
        })
        .fail(xhr => {
            const errs = xhr.responseJSON?.errors;
            Swal.fire('Error', errs ? Object.values(errs).flat().join('<br>') : 'No se pudo guardar.', 'error');
        });
    });

// ============================================================
// 🔥 FORZAR SCOPE GLOBAL
// ============================================================
window.verMascota      = verMascota;
window.editarMascota   = editarMascota;
window.eliminarMascota = eliminarMascota;
</script>
@endpush