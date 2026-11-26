@extends('layouts.app')

@section('subtitle', 'Roles y Permisos')

@section('content_body')
<br>

<div class="vc-page-header">
    <div class="vc-page-header__left">
        <span class="vc-page-icon"><i class="fas fa-user-shield"></i></span>
        <div>
            <h1>Roles y Permisos</h1>
            <p>Gestión de los roles y permisos del sistema</p>
        </div>
    </div>
    <div class="vc-page-header__right" style="margin-left:auto;">
        <button type="button" class="btn btn-success" id="btnNuevoRol">
            <i class="fas fa-plus"></i> Nuevo rol
        </button>
    </div>
</div>

{{-- ===================== LISTADO ===================== --}}
<div class="vc-card" id="rolesListado">
    <div class="vc-card__header">
        <span class="vc-card__title"><i class="fas fa-shield-alt"></i> Roles registrados ({{ $roles->count() }})</span>
    </div>
    <div class="vc-card__body" style="padding:0;">
        <div class="table-responsive">
            <table class="table table-hover table-striped" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Permisos</th>
                        <th class="text-center" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $rol)
                        <tr>
                            <td><strong>{{ $rol->name }}</strong></td>
                            <td>
                                @forelse ($rol->permissions as $permiso)
                                    <span class="badge badge-info">{{ $permiso->name }}</span>
                                @empty
                                    <span class="text-muted small">Sin permisos asignados</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary btn-editar-rol" 
                                        data-id="{{ $rol->id }}" data-name="{{ $rol->name }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info btn-permisos-rol" 
                                        data-id="{{ $rol->id }}" data-name="{{ $rol->name }}" title="Permisos">
                                    <i class="fas fa-key"></i>
                                </button>
                                @if ($rol->name !== 'Admin')
                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-rol" 
                                            data-id="{{ $rol->id }}" data-name="{{ $rol->name }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<br>

{{-- ===================== MODAL CREAR / EDITAR ROL ===================== --}}
<div class="modal fade" id="modal-rol" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form method="POST" id="formRol" action="{{ route('roles.store') }}">
                @csrf
                <input type="hidden" name="_method" id="r_method" value="PUT" disabled>
                <input type="hidden" name="_id" id="r_id" value="">
                <div class="modal-header" id="rModalHead" style="background: #28a745; color: white;">
                    <h5 class="modal-title" id="rModalTitulo"><i class="fas fa-plus"></i> Nuevo rol</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="rErrores" style="display:none;"></div>
                    <div class="form-group">
                        <label>Nombre del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="r_name" name="name" required maxlength="50">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="rBtnGuardar"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL PERMISOS DEL ROL ===================== --}}
<div class="modal fade" id="modal-permisos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form method="POST" id="formPermisos" action="">
                @csrf
                <div class="modal-header" style="background: #17a2b8; color: white;">
                    <h5 class="modal-title"><i class="fas fa-key"></i> Permisos del rol: <span id="permRolNombre"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($permisosAgrupados as $categoria => $lista)
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold text-capitalize" style="border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                    <i class="fas fa-folder-open text-muted"></i> {{ $categoria }}
                                </h6>
                                @foreach ($lista as $permiso)
                                    <div class="custom-control custom-checkbox ml-3 mb-1">
                                        <input type="checkbox" class="custom-control-input permiso-check" 
                                               name="permissions[]" value="{{ $permiso->name }}" 
                                               id="perm_{{ $permiso->id }}">
                                        <label class="custom-control-label" for="perm_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info" id="btnGuardarPermisos"><i class="fas fa-save"></i> Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@push('js')
<script>
$(document).ready(function() {
    const CFG = {
        update: "{{ route('roles.update', ['id' => '__ID__']) }}",
        store: "{{ route('roles.store') }}",
        destroy: "{{ route('roles.destroy', ['id' => '__ID__']) }}",
        sync: "{{ route('roles.syncPermissions', ['id' => '__ID__']) }}",
    };

    // 🔥 NUEVO ROL
    $('#btnNuevoRol').on('click', function() {
        $('#formRol')[0].reset();
        $('#r_id').val('');
        $('#r_method').prop('disabled', true).val('PUT');
        $('#formRol').attr('action', CFG.store);
        $('#rModalHead').css('background', '#28a745');
        $('#rModalTitulo').html('<i class="fas fa-plus"></i> Nuevo rol');
        $('#rBtnGuardar').removeClass('btn-warning').addClass('btn-success').html('<i class="fas fa-save"></i> Guardar');
        $('#rErrores').hide().empty();
        $('#modal-rol').modal('show');
    });

    // 🔥 EDITAR ROL
    $(document).on('click', '.btn-editar-rol', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#formRol')[0].reset();
        $('#r_id').val(id);
        $('#r_name').val(name);
        $('#r_method').prop('disabled', false).val('PUT');
        $('#formRol').attr('action', CFG.update.replace('__ID__', id));
        $('#rModalHead').css('background', '#f59e0b');
        $('#rModalTitulo').html('<i class="fas fa-edit"></i> Editar rol');
        $('#rBtnGuardar').removeClass('btn-success').addClass('btn-warning').html('<i class="fas fa-save"></i> Actualizar');
        $('#rErrores').hide().empty();
        $('#modal-rol').modal('show');
    });

    // 🔥 GUARDAR ROL (CREAR/EDITAR)
    $('#formRol').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#rBtnGuardar');
        const htmlBtn = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        $('#rErrores').hide().empty();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    $('#modal-rol').modal('hide');
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: res.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                let msg = errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Error al guardar');
                $('#rErrores').html(msg).show();
            },
            complete: function() {
                $btn.prop('disabled', false).html(htmlBtn);
            }
        });
    });

    // 🔥 ELIMINAR ROL
    $(document).on('click', '.btn-eliminar-rol', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar rol?',
            text: 'El rol "' + name + '" será eliminado permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: CFG.destroy.replace('__ID__', id),
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Eliminado', text: res.message, timer: 1500, showConfirmButton: false });
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo eliminar', 'error');
                    }
                });
            }
        });
    });

    // 🔥 ABRIR MODAL PERMISOS
    $(document).on('click', '.btn-permisos-rol', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#permRolNombre').text(name);
        $('#formPermisos').attr('action', CFG.sync.replace('__ID__', id));
        
        // Marcar los permisos que ya tiene el rol
        // (Esta parte asume que tienes una forma de obtener los permisos actuales, 
        // la haremos con un AJAX rápido o dejando los checks vacíos para que el usuario los marque)
        
        // Limpiar checks
        $('.permiso-check').prop('checked', false);

        // Cargar permisos actuales del rol vía AJAX
        $.ajax({
            url: '/roles/' + id + '/permisos-actuales', // 🔥 Requiere una ruta extra
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            success: function(res) {
                res.permissions.forEach(function(perm) {
                    $('.permiso-check[value="' + perm + '"]').prop('checked', true);
                });
            }
        });

        $('#modal-permisos').modal('show');
    });

    // 🔥 GUARDAR PERMISOS
    $('#formPermisos').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnGuardarPermisos');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    $('#modal-permisos').modal('hide');
                    Swal.fire({ icon: 'success', title: '¡Actualizado!', text: res.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1500);
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error al guardar', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Permisos');
            }
        });
    });
});
</script>
@endpush