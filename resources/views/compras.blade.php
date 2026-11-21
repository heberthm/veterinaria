@extends('layouts.app')

@section('titulo', 'Compras - VetFlow')

@push('css')
{{-- 🔥 CSS DE SELECT2 CON BOOTSTRAP 4 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    /* Forzar el estilo de Bootstrap en el Select2 */
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
    
    /* Asegurar que el dropdown se vea por encima del modal */
    .select2-container--open { z-index: 9999 !important; }
    .select2-dropdown { z-index: 9999 !important; }
</style>
@endpush

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-shopping-bag mr-2"></i>Compras
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Compras</li>
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
                        <i class="fas fa-list mr-2"></i>Registro de Compras
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-compra">
                            <i class="fas fa-plus"></i> Nueva Compra
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabla-compras" class="table table-hover table-striped" style="width:100%; font-size:12.5px;" >
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>N° Factura</th>
                                    <th>Proveedor</th>
                                    <th>Fecha</th>
                                    <th>Vencimiento</th>
                                    <th>Total</th>
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
{{-- 🔥 MODAL NUEVA COMPRA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modal-compra" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" id="modal-compra-header" style="background-color: #28a745; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i id="modal-compra-icono" class="fas fa-shopping-bag mr-2"></i>
                    <span id="modal-compra-titulo">Nueva Compra</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                                
                <form id="form-compra" action="{{ route('compras.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="compra_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Proveedor <span class="text-danger">*</span></label>
                                <!-- 🔥 ÚNICO SELECT DE PROVEEDOR -->
                                <select name="proveedor_id" id="select-proveedor" class="form-control" required style="width: 100%;">
                                    <option value="">Buscar proveedor...</option>
                                    @foreach(($proveedores ?? []) as $proveedor)
                                        <option value="{{ $proveedor->id }}">
                                            {{ $proveedor->nombre }} - {{ $proveedor->nit ?? 'Sin NIT' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Factura</label>
                                <input type="text" name="numero_factura" class="form-control" placeholder="Número de factura" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Compra <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_compra" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                                <label>Método de Pago</label>
                                <select name="metodo_pago" class="form-control">
                                    <option value="">Seleccionar...</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3"><i class="fas fa-boxes mr-1"></i> Productos</h5>

                    <!-- 🔥 FILA PARA AGREGAR PRODUCTO -->
                    <div class="card bg-light mb-3 p-3 border-0 shadow-sm">
                        <div class="row align-items-end">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <label class="font-weight-bold small text-muted">Buscar Producto</label>
                                <!-- 🔥 SELECT PARA BUSCAR PRODUCTOS A LA LISTA -->
                                <select id="select-producto" class="form-control" style="width: 100%;">
                                    <option value="">Buscar producto...</option>
                                    @foreach(($productos ?? []) as $producto)
                                        <option value="{{ $producto->id }}" 
                                                data-precio="{{ $producto->precio_compra }}" 
                                                data-iva="{{ $producto->iva_porcentaje }}" 
                                                data-nombre="{{ $producto->nombre }}">
                                            {{ $producto->nombre }} (Stock: {{ $producto->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold small text-muted">Cantidad</label>
                                <input type="number" id="input-cantidad" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="font-weight-bold small text-muted">Precio Compra</label>
                                <input type="number" id="input-precio" class="form-control" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" onclick="agregarProductoALista()">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tabla-productos-compra">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" style="width: 100px;">Cantidad</th>
                                    <th class="text-right" style="width: 120px;">Precio Unit.</th>
                                    <th class="text-center" style="width: 100px;">Desc. %</th>
                                    <th class="text-center" style="width: 100px;">IVA %</th>
                                    <th class="text-right" style="width: 120px;">Total</th>
                                    <th class="text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="productos-body">
                                <!-- Productos agregados dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td class="text-right" id="subtotal-display">$ 0</td>
                                </tr>
                                <tr>
                                    <td><strong>Descuento</strong></td>
                                    <td class="text-right" id="descuento-display">$ 0</td>
                                </tr>
                                <tr>
                                    <td><strong>IVA</strong></td>
                                    <td class="text-right" id="iva-display">$ 0</td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="text-right text-success" id="total-display"><strong>$ 0</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>

                    <div id="erroresCompra" class="text-danger small"></div>
                </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-guardar-compra" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL PARA VER COMPRA (estilo moderno)                    --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalVerCompra" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #343a40; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-eye mr-2"></i> Detalle de la Compra
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="detalleCompraBody" style="padding: 25px; background-color: #ffffff;">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando...</p>
                </div>
            </div>

            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL PARA ELIMINAR COMPRA                                --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalEliminarCompra" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background-color: #dc3545; color: white; border-radius: 12px 12px 0 0; padding: 15px 20px;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
                    <i class="fas fa-trash mr-2"></i> Eliminar Compra
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center" style="padding: 25px; background-color: #ffffff;">
                <div style="font-size: 44px; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i></div>
                <h5 class="mt-2 mb-3" style="font-weight: 700;">¿Eliminar esta compra?</h5>
                <div id="elim-compra-detalle" class="p-2 mb-3" style="background:#f8fafc;border-radius:8px;font-size:13px;"></div>
                <p class="text-muted mb-0" style="font-size: 13px;">Esta acción no se puede deshacer.</p>
                <div id="elim-compra-error" class="text-danger small mt-2"></div>
            </div>

            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar" style="border-radius: 8px; padding: 8px 20px; font-weight: 600;">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
(function () {
    'use strict';

    // Evita doble inicialización (doble registro de eventos = doble guardado)
    if (window.__comprasIniciado) return;
    window.__comprasIniciado = true;

    const CSRF = '{{ csrf_token() }}';

    const esc = (v) => String(v == null ? '' : v)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');

    const num = (v, d) => { const n = parseFloat(v); return isFinite(n) ? n : (d === undefined ? 0 : d); };
    const money = (n) => '$ ' + num(n).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

    let productosCompra = [];
    let contadorProductoCompra = 0;

    // Espera a jQuery + DataTables (el layout puede cargar este script antes)
    function esperar(cb, n) {
        n = n || 0;
        if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) return cb(window.jQuery);
        if (n > 200) return console.error('[Compras] jQuery/DataTables no están disponibles');
        setTimeout(function () { esperar(cb, n + 1); }, 50);
    }

    // Select2: si el layout no lo trae, se carga aquí
    function conSelect2($, cb) {
        if ($.fn.select2) return cb();
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
        s.onload = cb;
        s.onerror = function () { console.error('[Compras] No se pudo cargar select2'); };
        document.head.appendChild(s);
    }

    // ============================================================
    // CÁLCULOS (descuento % por línea → IVA sobre el valor ya descontado)
    // ============================================================
    function calcLinea(item) {
        const base  = item.cantidad * item.precio;
        const desc  = base * (item.descuento / 100);
        const neto  = base - desc;
        const iva   = neto * (item.iva / 100);
        return { base: base, desc: desc, iva: iva, total: neto + iva };
    }

    function calcularTotalesCompra() {
        let sub = 0, desc = 0, iva = 0;
        productosCompra.forEach(function (it) {
            const c = calcLinea(it);
            sub += c.base; desc += c.desc; iva += c.iva;
        });
        $('#subtotal-display').text(money(sub));
        $('#descuento-display').text(money(desc));
        $('#iva-display').text(money(iva));
        $('#total-display').html('<strong>' + money(sub - desc + iva) + '</strong>');
    }

    function renderizarProductosCompra() {
        let html = '';
        productosCompra.forEach(function (item, index) {
            html += `
            <tr>
                <td><input type="text" class="form-control form-control-sm" value="${esc(item.nombre)}" readonly></td>
                <td><input type="number" class="form-control form-control-sm text-center" value="${item.cantidad}" min="0.01" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'cantidad', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm text-right" value="${item.precio}" min="0" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'precio', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm text-center" value="${item.descuento}" min="0" max="100" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'descuento', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm text-center" value="${item.iva}" min="0" step="0.01"
                           onchange="actualizarProductoCompra(${index}, 'iva', this.value)"></td>
                <td class="text-right" id="total-linea-${index}">${money(calcLinea(item).total)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProductoCompra(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#productos-body').html(html);
    }

    // ============================================================
    // LISTA DE PRODUCTOS
    // ============================================================
    window.agregarProductoALista = function () {
        const select = $('#select-producto');
        const productoId = select.val();

        if (!productoId) {
            Swal.fire('Atención', 'Selecciona un producto', 'warning');
            return;
        }

        const opt = select.find('option:selected');
        const nombre   = String(opt.attr('data-nombre') || opt.text()).trim();
        const precio   = num($('#input-precio').val());
        const cantidad = num($('#input-cantidad').val(), 1);
        // IVA 0 es válido (productos exentos): solo se usa 19 si el dato no existe
        const ivaData  = parseFloat(opt.attr('data-iva'));
        const iva      = isFinite(ivaData) ? ivaData : 19;

        if (cantidad <= 0) {
            Swal.fire('Atención', 'La cantidad debe ser mayor a 0', 'warning');
            return;
        }

        const existente = productosCompra.find(function (i) { return String(i.producto_id) === String(productoId); });
        if (existente) {
            existente.cantidad += cantidad;
            if (precio > 0) existente.precio = precio;
        } else {
            productosCompra.push({
                id: contadorProductoCompra++,
                producto_id: productoId,
                nombre: nombre,
                cantidad: cantidad,
                precio: precio,
                descuento: 0,
                iva: iva
            });
        }

        select.val('').trigger('change');
        $('#input-cantidad').val(1);
        $('#input-precio').val('');

        renderizarProductosCompra();
        calcularTotalesCompra();
    };

    // No se vuelve a dibujar la tabla: así el cursor no se pierde al pasar de un campo a otro
    window.actualizarProductoCompra = function (index, campo, valor) {
        const item = productosCompra[index];
        if (!item) return;

        let n = num(valor);
        if (n < 0) n = 0;
        if (campo === 'descuento' && n > 100) n = 100;
        if (campo === 'cantidad' && n <= 0) n = 1;

        item[campo] = n;
        $('#total-linea-' + index).text(money(calcLinea(item).total));
        calcularTotalesCompra();
    };

    window.eliminarProductoCompra = function (index) {
        productosCompra.splice(index, 1);
        renderizarProductosCompra();
        calcularTotalesCompra();
    };

    function resetFormularioCompra() {
        $('#form-compra')[0].reset();
        $('#compra_id').val('');
        estiloModalCompra(false);
        $('#select-producto').val('').trigger('change');
        $('#select-proveedor').val('').trigger('change');
        productosCompra = [];
        renderizarProductosCompra();
        calcularTotalesCompra();
        $('#erroresCompra').empty();
    }

    // ============================================================
    // VER / EDITAR / ELIMINAR (ventanas modales)
    // ============================================================
    const val = (x, d) => (x === null || x === undefined ? d : x);
    const soloTexto = (h) => $('<div>').html(h == null ? '' : h).text();   // quita etiquetas HTML

    // Id del botón: data-id, número dentro del onclick o fila de DataTables
    function idDelBoton(el) {
        const $el = $(el);
        let id = $el.data('id') || $el.data('compra-id') || $el.closest('[data-id]').data('id');
        if (!id) {
            const m = String($el.attr('onclick') || '').match(/\((\d+)/);
            if (m) id = m[1];
        }
        if (!id && window.tablaCompras) {
            const fila = window.tablaCompras.row($el.closest('tr')).data();
            if (fila) id = fila.id;
        }
        return id;
    }

    // Pide la compra completa (JSON) a GET /compras/{id}
    function cargarCompra(id, ok, fail) {
        $.ajax({
            url: '/compras/' + id,
            method: 'GET',
            dataType: 'json',
            headers: { 'Accept': 'application/json' },
            success: function (r) {
                const c = (r && (r.compra || r.data)) || r;
                if (c && c.id) ok(c); else fail('La respuesta no trae la compra.');
            },
            error: function (xhr) {
                console.error('[Compras] GET /compras/' + id + ' HTTP ' + xhr.status,
                    (xhr.responseText || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').slice(0, 300));
                fail('HTTP ' + xhr.status);
            }
        });
    }

    // Líneas de la compra en el formato interno de la vista
    function lineasDe(c) {
        const lineas = c.detalles || c.items || c.detalle || c.productos || [];
        return lineas.map(function (d) {
            const prod = d.producto || {};
            return {
                id: contadorProductoCompra++,
                producto_id: val(d.producto_id, prod.id),
                nombre: val(d.nombre_producto, val(prod.nombre, 'Producto #' + val(d.producto_id, ''))),
                cantidad: num(d.cantidad, 1),
                precio: num(val(d.precio_unitario, val(d.precio_compra, d.precio))),
                descuento: num(val(d.descuento_porcentaje, d.descuento)),
                iva: num(val(d.iva_porcentaje, d.iva))
            };
        });
    }

    // ---------- VER ----------
    window.verCompra = function (id) {
        if (!id) return console.error('[Compras] ver: falta el id');

        // Sin spinner: el modal se abre cuando los datos ya están listos
        cargarCompra(id, function (c) {
            const items = lineasDe(c);
            let sub = 0, desc = 0, iva = 0;
            items.forEach(function (it) { const l = calcLinea(it); sub += l.base; desc += l.desc; iva += l.iva; });
            const total = isFinite(parseFloat(c.total)) ? parseFloat(c.total) : (sub - desc + iva);

            const prov = c.proveedor
                ? (typeof c.proveedor === 'object' ? (c.proveedor.nombre || '') : soloTexto(c.proveedor))
                : (c.proveedor_nombre || '');
            const estado = String(val(c.estado, '')).replace(/_/g, ' ');

            const fila = (icono, etiqueta, valor) => `
                <div class="d-flex align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                    <div class="mr-3 text-center" style="width:25px;color:#3b82f6;"><i class="${icono}"></i></div>
                    <div style="font-size:13px;"><strong>${etiqueta}:</strong> ${valor}</div>
                </div>`;

            const filasItems = items.length ? items.map(function (it) {
                return `<tr>
                    <td>${esc(it.nombre)}</td>
                    <td class="text-center">${it.cantidad}</td>
                    <td class="text-right">${money(it.precio)}</td>
                    <td class="text-center">${it.descuento}%</td>
                    <td class="text-center">${it.iva}%</td>
                    <td class="text-right">${money(calcLinea(it).total)}</td>
                </tr>`;
            }).join('') : '<tr><td colspan="6" class="text-center text-muted">Sin productos</td></tr>';

            $('#detalleCompraBody').html(`
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mr-3"
                         style="width:65px;height:65px;background:#3b82f6;font-size:24px;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="font-size:22px;">Compra #${esc(c.id)}</h4>
                        <div class="d-flex align-items-center mt-1">
                            <span class="text-muted small mr-2">${esc(val(c.numero_factura_proveedor, val(c.numero_factura, 'Sin factura')))}</span>
                            ${estado ? `<span class="badge badge-info" style="padding:4px 10px;border-radius:20px;text-transform:capitalize;">${esc(estado)}</span>` : ''}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-info-circle"></i> Información General</h6>
                        ${fila('fas fa-truck', 'Proveedor', esc(prov || '—'))}
                        ${fila('far fa-calendar', 'Fecha compra', esc(String(val(c.fecha_compra, '—')).substring(0, 10)))}
                        ${fila('far fa-calendar-times', 'Vencimiento', esc(c.fecha_vencimiento ? String(c.fecha_vencimiento).substring(0, 10) : '—'))}
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3" style="font-weight:700;font-size:15px;"><i class="fas fa-dollar-sign"></i> Pago y Totales</h6>
                        ${fila('fas fa-credit-card', 'Método de pago', esc(String(val(c.metodo_pago, '—')).replace(/_/g, ' ')))}
                        ${fila('fas fa-align-left', 'Observaciones', esc(val(c.observacion, val(c.observaciones, '—')) || '—'))}
                        ${fila('fas fa-cash-register', 'Total', '<strong>' + money(total) + '</strong>')}
                    </div>
                </div>

                <h6 class="mt-3 mb-2" style="font-weight:700;font-size:15px;"><i class="fas fa-boxes"></i> Productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" style="font-size:13px;">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Precio</th>
                                <th class="text-center">Desc.</th>
                                <th class="text-center">IVA</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>${filasItems}</tbody>
                    </table>
                </div>
                <div class="text-right" style="font-size:13px;">
                    <div>Subtotal: <strong>${money(sub)}</strong></div>
                    <div>Descuento: <strong>${money(desc)}</strong></div>
                    <div>IVA: <strong>${money(iva)}</strong></div>
                </div>`);
            $('#modalVerCompra').modal('show');
        }, function (msg) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la compra (' + msg + ').',
                timer: 3000, timerProgressBar: true, showConfirmButton: false });
        });
    };

    // ---------- EDITAR (el mismo modal de compra, en modo edición) ----------
    window.editarCompra = function (id) {
        if (!id) return console.error('[Compras] editar: falta el id');

        cargarCompra(id, function (c) {
            const f = $('#form-compra');

            resetFormularioCompra();
            $('#compra_id').val(c.id);
            estiloModalCompra(true);

            $('#select-proveedor').val(String(val(c.proveedor_id, ''))).trigger('change');
            f.find('[name=numero_factura]').val(val(c.numero_factura_proveedor, val(c.numero_factura, '')));
            f.find('[name=fecha_compra]').val(String(val(c.fecha_compra, '')).substring(0, 10));
            f.find('[name=fecha_vencimiento]').val(String(val(c.fecha_vencimiento, '')).substring(0, 10));
            f.find('[name=metodo_pago]').val(val(c.metodo_pago, ''));
            f.find('[name=observaciones]').val(val(c.observacion, val(c.observaciones, '')));

            productosCompra = lineasDe(c);
            renderizarProductosCompra();
            calcularTotalesCompra();
            restaurarBotonGuardar();

            $('#modal-compra').modal('show');
        }, function (msg) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la compra (' + msg + ').',
                timer: 3000, timerProgressBar: true, showConfirmButton: false });
        });
    };

    // ---------- ELIMINAR (modal de confirmación) ----------
    let compraAEliminar = null;

    window.eliminarCompra = function (id) {
        if (!id) return console.error('[Compras] eliminar: falta el id');
        compraAEliminar = id;

        // Datos de la fila para mostrar qué se va a borrar
        let fila = null;
        if (window.tablaCompras) {
            window.tablaCompras.rows().every(function () {
                const d = this.data();
                if (String(d.id) === String(id)) fila = d;
            });
        }
        const partes = ['<strong>Compra #' + esc(id) + '</strong>'];
        if (fila) {
            if (fila.numero_factura_proveedor) partes.push('Factura: ' + esc(soloTexto(fila.numero_factura_proveedor)));
            if (fila.proveedor)                partes.push('Proveedor: ' + esc(soloTexto(fila.proveedor)));
            if (fila.total)                    partes.push('Total: ' + esc(soloTexto(fila.total)));
        }
        $('#elim-compra-detalle').html(partes.join('<br>'));
        $('#elim-compra-error').empty();
        $('#btn-confirmar-eliminar').prop('disabled', false).html('<i class="fas fa-trash"></i> Eliminar');
        $('#modalEliminarCompra').modal('show');
    };

    function confirmarEliminarCompra() {
        const btn = $('#btn-confirmar-eliminar');
        if (!compraAEliminar || btn.prop('disabled')) return;

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        $('#elim-compra-error').empty();

        $.ajax({
            url: '/compras/' + compraAEliminar,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            success: function (response) {
                if (response && response.success === false) {
                    $('#elim-compra-error').text(response.message || 'No se pudo eliminar');
                    btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Eliminar');
                    return;
                }
                $('#modalEliminarCompra').modal('hide');
                compraAEliminar = null;
                Swal.fire({ icon: 'success', title: 'Eliminado',
                    text: (response && response.message) || 'Compra eliminada',
                    timer: 1500, timerProgressBar: true, showConfirmButton: false });
                window.tablaCompras.ajax.reload(null, false);
            },
            error: function (xhr) {
                $('#elim-compra-error').text((xhr.responseJSON && xhr.responseJSON.message)
                    || ('Error al eliminar (HTTP ' + xhr.status + ')'));
                btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Eliminar');
            }
        });
    }

    // ---------- Aspecto del modal de compra: verde = nueva, naranja = editar ----------
    function estiloModalCompra(editando) {
        $('#modal-compra-header').css('background-color', editando ? '#f59e0b' : '#28a745');
        $('#modal-compra-titulo').text(editando ? 'Editar Compra' : 'Nueva Compra');
        $('#modal-compra-icono').attr('class', (editando ? 'fas fa-edit' : 'fas fa-shopping-bag') + ' mr-2');
        $('#btn-guardar-compra').toggleClass('btn-warning', editando).toggleClass('btn-success', !editando);
    }

    function restaurarBotonGuardar() {
        const editando = !!$('#compra_id').val();
        $('#btn-guardar-compra').prop('disabled', false)
            .html('<i class="fas fa-save"></i> ' + (editando ? 'Actualizar' : 'Guardar'));
    }

    // ============================================================
    // GUARDAR COMPRA (blindado contra doble envío)
    // ============================================================
    function guardarCompra(e) {
        if (e) { e.preventDefault(); e.stopImmediatePropagation(); }

        const btn = $('#btn-guardar-compra');
        if (btn.prop('disabled')) return;

        const form = $('#form-compra');
        const proveedor = form.find('[name=proveedor_id]').val();
        const fCompra   = form.find('[name=fecha_compra]').val();
        const fVence    = form.find('[name=fecha_vencimiento]').val();

        // Validaciones en el navegador (el form no se envía de forma nativa, no corre el "required")
        if (!proveedor) return Swal.fire('Atención', 'Selecciona un proveedor', 'warning');
        if (!fCompra)   return Swal.fire('Atención', 'Indica la fecha de compra', 'warning');
        if (fVence && fVence < fCompra)
            return Swal.fire('Atención', 'La fecha de vencimiento no puede ser anterior a la fecha de compra', 'warning');
        if (productosCompra.length === 0)
            return Swal.fire('Atención', 'Agrega al menos un producto a la compra', 'warning');

        $('#erroresCompra').empty();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        const compraId = $('#compra_id').val();
        const obs = form.find('[name=observaciones]').val();
        const payload = {
            _token: CSRF,
            proveedor_id: proveedor,
            numero_factura: form.find('[name=numero_factura]').val(),
            numero_factura_proveedor: form.find('[name=numero_factura]').val(),   // nombre de la columna en la tabla
            fecha_compra: fCompra,
            fecha_vencimiento: fVence,
            metodo_pago: form.find('[name=metodo_pago]').val(),
            observacion: obs,        // nombre que usaba la vista
            observaciones: obs,      // nombre del campo del formulario
            items: productosCompra.map(function (it) {
                return {
                    producto_id: it.producto_id,
                    cantidad: it.cantidad,
                    precio_unitario: it.precio,
                    descuento: it.descuento,       // porcentaje
                    iva_porcentaje: it.iva
                };
            })
        };

        $.ajax({
            url: compraId ? '/compras/' + compraId : form.attr('action'),
            method: compraId ? 'PUT' : 'POST',
            data: payload,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            success: function (response) {
                if (response && response.success === false) {
                    Swal.fire('Error', response.message || 'Error al guardar', 'error');
                    return;
                }
                $('#modal-compra').modal('hide');
                Swal.fire({ icon: 'success', title: 'Éxito',
                    text: (response && response.message) || (compraId ? 'Compra actualizada' : 'Compra guardada'),
                    showConfirmButton: false, timer: 1500, timerProgressBar: true });
                window.tablaCompras.ajax.reload(null, false);
                resetFormularioCompra();
            },
            error: function (xhr) {
                const j = xhr.responseJSON || {};
                const html = j.errors
                    ? Object.values(j.errors).flat().map(esc).join('<br>')
                    : esc(j.message || 'Error al guardar la compra');
                console.error('[Compras] guardar HTTP ' + xhr.status, j.errors || j.message || xhr.responseText);
                $('#erroresCompra').html(html);
                Swal.fire({ icon: 'error', title: 'No se pudo guardar', html: html });
            },
            complete: function () {
                restaurarBotonGuardar();
            }
        });
    }

    // ============================================================
    // ARRANQUE
    // ============================================================
    esperar(function ($) {
        $(function () {

            // ---------- DataTable ----------
            if ($.fn.DataTable.isDataTable('#tabla-compras')) {
                $('#tabla-compras').DataTable().destroy();
            }
            $.fn.dataTable.ext.errMode = 'none';
            $('#tabla-compras').on('error.dt', function (e, s, t, msg) { console.error('[Compras] DataTables:', msg); });

            window.tablaCompras = $('#tabla-compras').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('compras.datatable') }}",
                    error: function (xhr) {
                        console.error('[Compras] datatable HTTP ' + xhr.status,
                            (xhr.responseText || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').slice(0, 400));
                        Swal.fire('Error', (xhr.responseJSON && xhr.responseJSON.message)
                            || ('No se pudo cargar las compras (HTTP ' + xhr.status + ').'), 'error');
                    }
                },
                columns: [
                    { data: 'id', name: 'id', className: 'text-center', width: '50px' },
                    { data: 'numero_factura_proveedor', name: 'numero_factura_proveedor' },
                    // Columnas calculadas: sin búsqueda/orden para no provocar "Unknown column"
                    { data: 'proveedor', name: 'proveedor', orderable: false, searchable: false },
                    { data: 'fecha_compra', name: 'fecha_compra', className: 'text-center' },
                    { data: 'fecha_vencimiento', name: 'fecha_vencimiento', className: 'text-center' },
                    { data: 'total', name: 'total', className: 'text-right' },
                    { data: 'estado', name: 'estado', className: 'text-center', orderable: false, searchable: false },
                    {
                        // Los tres botones se dibujan aquí, sin depender de lo que devuelva el controlador
                        data: null, name: 'acciones', orderable: false, searchable: false,
                        className: 'text-center text-nowrap', width: '120px',
                        render: function (data, type, row) {
                            const id = row.id;
                            return '<button type="button" class="btn btn-info btn-sm btn-ver-compra mr-1" data-id="' + id + '" title="Ver">'
                                 +   '<i class="fas fa-eye"></i></button>'
                                 + '<button type="button" class="btn btn-warning btn-sm btn-editar-compra mr-1" data-id="' + id + '" title="Editar">'
                                 +   '<i class="fas fa-edit"></i></button>'
                                 + '<button type="button" class="btn btn-danger btn-sm btn-eliminar-compra" data-id="' + id + '" title="Eliminar">'
                                 +   '<i class="fas fa-trash"></i></button>';
                        }
                    }
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
                    paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
                }
            });

            // ---------- Select2 (proveedor y producto) ----------
            conSelect2($, function () {
                $('#select-proveedor').select2({
                    theme: 'bootstrap4', width: '100%',
                    placeholder: 'Buscar proveedor...', allowClear: true,
                    dropdownParent: $('#modal-compra')
                });
                $('#select-producto').select2({
                    theme: 'bootstrap4', width: '100%',
                    placeholder: 'Buscar producto...', allowClear: true,
                    dropdownParent: $('#modal-compra')
                });
            });

            // Autocompletar precio al elegir producto (si se limpia la selección no se toca)
            $('#select-producto').on('change', function () {
                if (!$(this).val()) return;
                const precio = $(this).find('option:selected').attr('data-precio');
                $('#input-precio').val(isFinite(parseFloat(precio)) ? parseFloat(precio) : 0);
            });

            // Enter en cantidad/precio agrega el producto (y no envía el formulario)
            $('#input-cantidad, #input-precio').on('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); window.agregarProductoALista(); }
            });
            $('#form-compra').on('submit', function (e) { e.preventDefault(); });

            // ---------- Botones de la tabla (ver / editar / eliminar) ----------
            const SEL_VER      = '.btn-ver-compra, .btn-ver, .btn-view, .ver-compra, [data-accion="ver"]';
            const SEL_EDITAR   = '.btn-editar-compra, .btn-editar, .btn-edit, .editar-compra, [data-accion="editar"]';
            const SEL_ELIMINAR = '.btn-eliminar-compra, .btn-eliminar, .btn-delete, .eliminar-compra, [data-accion="eliminar"]';

            $('#tabla-compras').on('click', SEL_VER, function (e) {
                if ($(this).attr('onclick')) return;      // ya tiene su propio onclick
                e.preventDefault(); window.verCompra(idDelBoton(this));
            });
            $('#tabla-compras').on('click', SEL_EDITAR, function (e) {
                if ($(this).attr('onclick')) return;
                e.preventDefault(); window.editarCompra(idDelBoton(this));
            });
            $('#tabla-compras').on('click', SEL_ELIMINAR, function (e) {
                if ($(this).attr('onclick')) return;
                e.preventDefault(); window.eliminarCompra(idDelBoton(this));
            });

            $('#btn-confirmar-eliminar').off('click.compraElim').on('click.compraElim', confirmarEliminarCompra);

            // ---------- Guardar ----------
            $(document).off('click.compraGuardar', '#btn-guardar-compra')
                       .on('click.compraGuardar', '#btn-guardar-compra', guardarCompra);

            // ---------- Limpiar al cerrar ----------
            $('#modal-compra').on('hidden.bs.modal', function () {
                resetFormularioCompra();
                restaurarBotonGuardar();
            });
        });
    });
})();
</script>
@stop