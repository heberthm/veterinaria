@extends('layouts.app')

@section('subtitle', 'Usuarios')

@section('content_body')

<br>

<div class="vc-page-header">
    <div class="vc-page-header__left">
        <span class="vc-page-icon"><i class="fas fa-users-cog"></i></span>
        <div>
            <h1>Usuarios</h1>
            <p>Gestión de los usuarios y roles de la clínica</p>
        </div>
    </div>
    <div class="vc-page-header__right" style="margin-left:auto;">
        <button type="button" class="btn btn-success" id="btnNuevoUsuario">
            <i class="fas fa-user-plus"></i> Nuevo usuario
        </button>
    </div>
</div>

{{-- ===================== BÚSQUEDA ===================== --}}
{{-- ===================== BÚSQUEDA ===================== --}}
<div class="vc-card us-busqueda">
    <div class="vc-card__body">
        <div class="us-busqueda__form">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" name="q" id="usBuscar" value="{{ $q }}" class="form-control" autocomplete="off"
                       placeholder="Buscar por nombre, correo o rol...">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="usLimpiar"
                            style="{{ $q === '' ? 'display:none;' : '' }}">Limpiar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== LISTADO ===================== --}}
<div class="vc-card" id="usListado">
    <div class="vc-card__header">
        <span class="vc-card__title"><i class="fas fa-users"></i> Usuarios registrados ({{ $users->total() }})</span>
    </div>
    <div class="vc-card__body" style="padding:0;">
        <div class="us-scroll">
            <table class="us-tabla">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th class="us-c">Estado</th>
                        <th class="us-c" style="width:150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        @php
                            $datos = [
                                'id'           => $u->id,
                                'name'         => $u->name,
                                'email'        => $u->email,
                                'rol'          => $u->rol,
                                'telefono'     => $u->telefono,
                                'especialidad' => $u->especialidad,
                                'activo'       => (bool) $u->activo,
                                'propio'       => auth()->id() === $u->id,
                                'super'        => (bool) $u->es_super_admin,
                                'foto'         => $u->adminlte_image(),
                                'creado'       => optional($u->created_at)->format('d/m/Y'),
                            ];
                            $claseRol = [
                                'Admin'         => 'rol-admin',
                                'Veterinario'   => 'rol-vet',
                                'Recepcionista' => 'rol-recep',
                                'Cajero'        => 'rol-cajero',
                            ][$u->rol] ?? '';
                        @endphp
                        <tr>
                            <td>
                                <div class="us-user">
                                    <img src="{{ $u->adminlte_image() }}" alt="" class="us-avatar">
                                    <div>
                                        <strong>{{ $u->name }}</strong>
                                        @if ($u->es_super_admin)
                                            <span class="us-badge rol-admin">Super admin</span>
                                        @endif
                                        @if (auth()->id() === $u->id)
                                            <span class="us-badge">Tú</span>
                                        @endif
                                        <div class="us-mail">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="us-badge {{ $claseRol }}">{{ $u->rol ?: 'Sin rol' }}</span></td>
                            <td>{{ $u->telefono ?: '—' }}</td>
                            <td>{{ $u->especialidad ?: '—' }}</td>
                            <td class="us-c">
                                <span class="us-badge {{ $u->activo ? 'est-ok' : 'est-no' }}">
                                    {{ $u->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="us-c">
                                <button type="button" class="btn btn-sm btn-dark us-ver"
                                        data-usuario="{{ json_encode($datos) }}" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning us-editar"
                                        data-usuario="{{ json_encode($datos) }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if (auth()->id() !== $u->id)
                                    <button type="button" class="btn btn-sm btn-danger us-eliminar"
                                            data-usuario="{{ json_encode($datos) }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="us-vacio">No hay usuarios para mostrar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="us-paginacion">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<br>

{{-- ===================== MODAL CREAR / EDITAR ===================== --}}
<div class="modal fade" id="modal-usuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content us-modal">
            <form method="POST" id="formUsuario" action="{{ route('users.store') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="_method" id="u_method" value="PUT" disabled>
                <input type="hidden" name="_modo" id="u_modo" value="crear">
                <input type="hidden" name="_id" id="u_id" value="">

                <div class="modal-header us-modal__head" id="usModalHead">
                    <h5 class="modal-title" id="usModalTitulo"><i class="fas fa-user-plus"></i> Nuevo usuario</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-danger" id="usErrores" style="display:none;"></div>

                    <div class="form-group">
                        <label for="u_name">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="u_name" name="name" maxlength="60" required>
                    </div>

                    <div class="form-group">
                        <label for="u_email">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="u_email" name="email" maxlength="180" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="u_rol">Rol <span class="text-danger">*</span></label>
                            <select class="form-control" id="u_rol" name="rol" required>
                                <option value="">Seleccionar...</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol }}">{{ $rol }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="u_telefono">Teléfono</label>
                            <input type="text" class="form-control" id="u_telefono" name="telefono">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="u_especialidad">Especialidad</label>
                        <input type="text" class="form-control" id="u_especialidad" name="especialidad"
                               placeholder="Ej: Medicina interna, Cirugía...">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="u_password">Contraseña <span class="text-danger" id="u_pass_req">*</span></label>
                            <input type="password" class="form-control" id="u_password" name="password" autocomplete="new-password">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="u_password_confirmation">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="u_password_confirmation"
                                   name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3" id="u_pass_hint">Mínimo 8 caracteres.</small>

                    <div class="custom-control custom-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="custom-control-input" id="u_activo" name="activo" value="1" checked>
                        <label class="custom-control-label" for="u_activo">Usuario activo (puede iniciar sesión)</label>
                    </div>

                    <div class="alert alert-info mt-3 mb-0" id="usAvisoPropio" style="display:none;">
                        <i class="fas fa-info-circle"></i>
                        No puedes cambiar tu propio rol ni tu estado.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="usBtnGuardar">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL VER ===================== --}}
<div class="modal fade" id="modalVerUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content us-modal">
            <div class="modal-header us-modal__head" style="background:#343a40;">
                <h5 class="modal-title"><i class="fas fa-user"></i> Detalle del usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="verUsuarioBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL ELIMINAR ===================== --}}
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content us-modal">
            <form method="POST" id="formEliminarUsuario" action="">
                @csrf
                @method('DELETE')
                <div class="modal-header us-modal__head" style="background:#dc3545;">
                    <h5 class="modal-title"><i class="fas fa-trash"></i> Eliminar usuario</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-1" style="font-size:42px;color:#dc3545;"><i class="fas fa-exclamation-circle"></i></p>
                    <p class="mb-1">¿Seguro que deseas eliminar a</p>
                    <p><strong id="elimUsuarioNombre"></strong>?</p>
                    <small class="text-muted">Esta acción no se puede deshacer. Si solo quieres quitarle el acceso, edítalo y desactívalo.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btnConfirmarEliminarUsuario">
                        <i class="fas fa-trash"></i> Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@push('css')
<style>
    .us-busqueda { margin-bottom: 16px; }
    #usListado { transition: opacity .15s; }
    #usListado.us-cargando { opacity: .5; pointer-events: none; }
    .us-scroll { overflow-x: auto; }
    .us-tabla { width: 100%; border-collapse: collapse; font-size: 13px; }
    .us-tabla th {
        background: #F1F5F9; color: #475569; font-size: 12px; text-align: left;
        padding: 10px 14px; border-bottom: 1px solid #E2E8F0; white-space: nowrap;
    }
    .us-tabla td { padding: 10px 14px; border-bottom: 1px solid #EEF1F6; color: #1e293b; vertical-align: middle; }
    .us-tabla tbody tr:hover { background: #F8FAFF; }
    .us-c { text-align: center !important; }
    .us-vacio { text-align: center; color: #94a3b8; padding: 30px 12px !important; }
    .us-paginacion { padding: 12px 14px 0; border-top: 1px solid #EEF1F6; }

    .us-user { display: flex; align-items: center; gap: 12px; }
    .us-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex: 0 0 38px; }
    .us-mail { color: #64748b; font-size: 12px; }

    .us-badge {
        display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
        background: #E2E8F0; color: #475569; margin-left: 4px;
    }
    .us-tabla td > .us-badge:first-child { margin-left: 0; }
    .us-badge.rol-admin  { background: #EDE9FE; color: #6d28d9; }
    .us-badge.rol-vet    { background: #DBEAFE; color: #1d4ed8; }
    .us-badge.rol-recep  { background: #CCFBF1; color: #0f766e; }
    .us-badge.rol-cajero { background: #FEF3C7; color: #b45309; }
    .us-badge.est-ok     { background: #DCFCE7; color: #15803d; }
    .us-badge.est-no     { background: #FEE2E2; color: #b91c1c; }

    .us-modal { border: 0; border-radius: 12px; overflow: hidden; }
    .us-modal__head { color: #fff; border-bottom: 0; }
    .us-modal__head .modal-title { font-size: 17px; font-weight: 600; }

    .us-ver-cab { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .us-ver-cab img { width: 64px; height: 64px; border-radius: 50%; object-fit: cover; }
    .us-ver-lista { list-style: none; margin: 0; padding: 0; }
    .us-ver-lista li { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-bottom: 1px solid #EEF1F6; }
    .us-ver-lista li span:first-child { color: #64748b; }
    .us-ver-lista li span:last-child { font-weight: 600; text-align: right; word-break: break-word; }
</style>
@endpush

@push('js')
@php
    // Se arma aquí porque la directiva json de Blade separa por comas y rompe arreglos con varias claves
    $cfg = [
        'update'  => route('users.update', ['user' => '__ID__']),
        'store'   => route('users.store'),
        'destroy' => route('users.destroy', ['user' => '__ID__']),
        'flash'   => ['success' => session('success'), 'error' => session('error')],
        'errores' => $errors->all(),
        'modo'    => old('_modo'),
        'id'      => old('_id'),
        'old'     => [
            'name'         => old('name'),
            'email'        => old('email'),
            'rol'          => old('rol'),
            'telefono'     => old('telefono'),
            'especialidad' => old('especialidad'),
            'activo'       => old('activo'),
        ],
    ];
@endphp
<script>
(function () {
    if (window.__usuariosIniciado) return;
    window.__usuariosIniciado = true;

    const CFG = @json($cfg);

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    /* ---------- búsqueda en vivo y paginación (sin jQuery, sin recargar) ---------- */
    (function () {
        const porId = (i) => document.getElementById(i);
        let ultimaCarga = 0;
        let temporizador = null;

        async function cargarListado(urlStr) {
            const miCarga = ++ultimaCarga;                 // descarta respuestas viejas
            const actual = porId('usListado');
            if (actual) actual.classList.add('us-cargando');

            try {
                const res = await fetch(urlStr, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);

                const html = await res.text();
                if (miCarga !== ultimaCarga) return;        // llegó otra búsqueda después

                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nuevo = doc.querySelector('#usListado');
                const vivo = porId('usListado');
                if (!nuevo || !vivo) throw new Error('La respuesta no trae el listado');

                vivo.replaceWith(nuevo);
                window.history.replaceState({}, '', urlStr);
            } catch (e) {
                console.error('[Usuarios] no se pudo cargar el listado', e);
                const vivo = porId('usListado');
                if (vivo) vivo.classList.remove('us-cargando');
                if (window.Swal) Swal.fire({ icon: 'error', title: 'No se pudo actualizar el listado.', timer: 1800, showConfirmButton: false });
            }
        }

        function buscar() {
            const input = porId('usBuscar');
            if (!input) return;

            const q = input.value.trim();
            const url = new URL(window.location.href);
            if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
            url.searchParams.delete('page');

            const limpiar = porId('usLimpiar');
            if (limpiar) limpiar.style.display = q ? '' : 'none';

            cargarListado(url.toString());
        }

               function iniciar() {
            const input = porId('usBuscar');
            if (!input) return;

            // 🔥 Búsqueda en vivo: se lanza sola al dejar de teclear
            input.addEventListener('input', function () {
                clearTimeout(temporizador);
                temporizador = setTimeout(buscar, 300); // 300ms de espera (debounce)
            });

            // Enter también busca (sin recargar)
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { 
                    e.preventDefault(); 
                    clearTimeout(temporizador); 
                    buscar(); 
                }
            });

            // Botón limpiar
            const limpiar = porId('usLimpiar');
            if (limpiar) limpiar.addEventListener('click', function () {
                input.value = '';
                input.focus();
                buscar();
            });
        }


        // Paginación sin recargar
        document.addEventListener('click', function (e) {
            const a = e.target.closest ? e.target.closest('#usListado .pagination a') : null;
            if (!a) return;
            e.preventDefault();
            const href = a.getAttribute('href');
            if (href && href !== '#') cargarListado(href);
        });

        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
        else iniciar();
    })();

    // El layout puede imprimir este script antes que jQuery: se espera a que exista
    function esperar(fn, n) {
        n = n || 0;
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) { fn(window.jQuery); return; }
        if (n < 150) setTimeout(function () { esperar(fn, n + 1); }, 100);
        else console.error('[Usuarios] jQuery/Bootstrap no se cargaron.');
    }

    esperar(function ($) {

        const $form = $('#formUsuario');

        /* ---------- aviso (se cierra solo) ---------- */
        function aviso(tipo, texto) {
            if (window.Swal) {
                Swal.fire({
                    icon: tipo, title: texto, timer: 1800, timerProgressBar: true,
                    showConfirmButton: false, toast: false
                });
            } else {
                const div = $('<div class="alert alert-' + (tipo === 'success' ? 'success' : 'danger') + '">' + esc(texto) + '</div>');
                $('.vc-page-header').after(div);
                setTimeout(function () { div.fadeOut(300, function () { div.remove(); }); }, 2500);
            }
        }

        /* ---------- abrir formulario (crear / editar) ---------- */
        function abrirForm(modo, u, errores) {
            const editando = modo === 'editar';
            u = u || {};

            $('#u_modo').val(modo);
            $('#u_id').val(editando ? (u.id || '') : '');
            $('#u_method').prop('disabled', !editando).val('PUT');
            $form.attr('action', editando ? CFG.update.replace('__ID__', u.id) : CFG.store);

            $('#usModalHead').css('background', editando ? '#f59e0b' : '#28a745');
            $('#usModalTitulo').html(editando
                ? '<i class="fas fa-user-edit"></i> Editar usuario'
                : '<i class="fas fa-user-plus"></i> Nuevo usuario');

            $('#usBtnGuardar')
                .removeClass('btn-success btn-warning')
                .addClass(editando ? 'btn-warning' : 'btn-success')
                .prop('disabled', false)
                .html('<i class="fas fa-save"></i> ' + (editando ? 'Actualizar' : 'Guardar'));

            $('#u_name').val(u.name || '');
            $('#u_email').val(u.email || '');
            $('#u_rol').val(u.rol || '');
            $('#u_telefono').val(u.telefono || '');
            $('#u_especialidad').val(u.especialidad || '');
            $('#u_password, #u_password_confirmation').val('');
            $('#u_activo').prop('checked', u.activo === undefined || u.activo === null ? true : !!Number(u.activo === true ? 1 : u.activo));

            $('#u_password').prop('required', !editando);
            $('#u_pass_req').toggle(!editando);
            $('#u_pass_hint').text(editando
                ? 'Déjala en blanco para conservar la contraseña actual.'
                : 'Mínimo 8 caracteres.');

            $('#usAvisoPropio').toggle(!!u.propio);

            if (errores && errores.length) {
                $('#usErrores').html(errores.map(esc).join('<br>')).show();
            } else {
                $('#usErrores').hide().empty();
            }

            $('#modal-usuario').modal('show');
        }

        const datosDe = (el) => {
            try { return JSON.parse(el.getAttribute('data-usuario') || '{}'); }
            catch (e) { console.error('[Usuarios] data-usuario inválido', e); return {}; }
        };

        /* ---------- eventos ---------- */
        $('#btnNuevoUsuario').on('click', function () { abrirForm('crear'); });

        $(document).on('click', '.us-editar', function () { abrirForm('editar', datosDe(this)); });

        $(document).on('click', '.us-ver', function () {
            const u = datosDe(this);
            $('#verUsuarioBody').html(
                '<div class="us-ver-cab">' +
                    '<img src="' + esc(u.foto) + '" alt="">' +
                    '<div><h5 class="mb-0">' + esc(u.name) + '</h5>' +
                    '<span class="text-muted">' + esc(u.email) + '</span></div>' +
                '</div>' +
                '<ul class="us-ver-lista">' +
                    '<li><span>Rol</span><span>' + esc(u.rol || 'Sin rol') + (u.super ? ' (Super admin)' : '') + '</span></li>' +
                    '<li><span>Teléfono</span><span>' + esc(u.telefono || '—') + '</span></li>' +
                    '<li><span>Especialidad</span><span>' + esc(u.especialidad || '—') + '</span></li>' +
                    '<li><span>Estado</span><span>' + (u.activo ? 'Activo' : 'Inactivo') + '</span></li>' +
                    '<li><span>Creado</span><span>' + esc(u.creado || '—') + '</span></li>' +
                '</ul>'
            );
            $('#modalVerUsuario').modal('show');
        });

        $(document).on('click', '.us-eliminar', function () {
            const u = datosDe(this);
            $('#elimUsuarioNombre').text(u.name || '');
            $('#formEliminarUsuario').attr('action', CFG.destroy.replace('__ID__', u.id));
            $('#btnConfirmarEliminarUsuario').prop('disabled', false).html('<i class="fas fa-trash"></i> Sí, eliminar');
            $('#modalEliminarUsuario').modal('show');
        });

        /* ---------- refrescar el listado sin recargar la página ---------- */
        async function refrescarListado(irAPrimeraPagina) {
            try {
                const url = new URL(window.location.href);
                if (irAPrimeraPagina) url.searchParams.delete('page');

                const res = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nuevo = doc.querySelector('#usListado');
                const actual = document.querySelector('#usListado');

                if (nuevo && actual) {
                    actual.replaceWith(nuevo);
                    if (irAPrimeraPagina) window.history.replaceState({}, '', url.toString());
                } else {
                    window.location.reload(); // último recurso si la respuesta no trae el listado
                }
            } catch (e) {
                console.error('[Usuarios] no se pudo refrescar el listado', e);
                window.location.reload();
            }
        }

        /* ---------- guardar / actualizar por AJAX ---------- */
        let enviando = false;

        $form.on('submit', async function (e) {
            e.preventDefault();
            if (enviando) return;
            enviando = true;

            const $btn = $('#usBtnGuardar');
            const htmlBtn = $btn.html();
            const eraCrear = $('#u_modo').val() === 'crear';

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
            $('#usErrores').hide().empty();

            try {
                const res = await fetch($form.attr('action'), {
                    method: 'POST',                       // la actualización va como POST + _method=PUT
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData($form[0]),
                    credentials: 'same-origin'
                });

                const texto = await res.text();
                let json = null;
                try { json = JSON.parse(texto); } catch (err) { /* no era JSON */ }

                if (res.ok && json && json.success) {
                    $('#modal-usuario').modal('hide');
                    await refrescarListado(eraCrear);
                    aviso('success', json.message || 'Guardado correctamente.');
                    return;
                }

                let mensajes = [];
                if (res.status === 422 && json && json.errors) {
                    mensajes = Object.values(json.errors).reduce(function (a, b) { return a.concat(b); }, []);
                } else if (res.status === 419) {
                    mensajes = ['La sesión expiró. Recarga la página e inténtalo de nuevo.'];
                } else {
                    console.error('[Usuarios] HTTP ' + res.status, texto.slice(0, 600));
                    mensajes = [(json && json.message) || ('Error ' + res.status + ' al guardar el usuario.')];
                }
                $('#usErrores').html(mensajes.map(esc).join('<br>')).show();
            } catch (err) {
                console.error('[Usuarios] error de red', err);
                $('#usErrores').text('No se pudo conectar con el servidor.').show();
            } finally {
                enviando = false;
                $btn.prop('disabled', false).html(htmlBtn);
            }
        });

        // Evita doble envío al eliminar (este formulario sí recarga la página)
        $('#formEliminarUsuario').on('submit', function () {
            $('#btnConfirmarEliminarUsuario').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        });

        /* ---------- avisos del servidor ---------- */
        if (CFG.errores.length && CFG.modo) {
            // La validación falló: se reabre el modal con lo que escribió el usuario
            abrirForm(CFG.modo, Object.assign({ id: CFG.id, propio: false }, CFG.old, {
                activo: CFG.old.activo === null ? true : CFG.old.activo === '1'
            }), CFG.errores);
        } else if (CFG.flash.success) {
            aviso('success', CFG.flash.success);
        } else if (CFG.flash.error) {
            aviso('error', CFG.flash.error);
        }
    });
})();
</script>
@endpush