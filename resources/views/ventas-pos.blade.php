@extends('layouts.app')

@section('titulo', 'Ventas / POS')

@php
    $iconoPorProducto = function ($producto) {
        $nombre = strtolower($producto->nombre . ' ' . ($producto->categoria->nombre ?? ''));
        return match (true) {
            str_contains($nombre, 'vacuna')     => 'fa-syringe',
            str_contains($nombre, 'shampoo')    => 'fa-pump-soap',
            str_contains($nombre, 'alimento')   => 'fa-bone',
            str_contains($nombre, 'collar')     => 'fa-ring',
            str_contains($nombre, 'juguete')    => 'fa-baseball-ball',
            str_contains($nombre, 'arena')      => 'fa-cubes',
            str_contains($nombre, 'antipulga')  => 'fa-bug',
            default                              => 'fa-capsules',
        };
    };
@endphp

@section('content_body')

    <div class="vc-page-header">
        <div class="vc-page-header__left">
            <span class="vc-page-icon" style="background: var(--vc-blue-soft); color: var(--vc-blue);"><i class="fas fa-shopping-cart"></i></span>
            <div>
                <h1>Ventas / POS</h1>
                <p>Registra ventas de productos y servicios en caja</p>
            </div>
        </div>
        <div class="vc-breadcrumb">
            <a href="{{ route('inicio') }}">Inicio</a> &rsaquo; <a href="#">Ventas</a> &rsaquo; <span class="current">POS</span>
        </div>
    </div>

    @if ($cajaActual)
        <div class="alert alert-light d-flex align-items-center justify-content-between mb-3" style="border:1px solid var(--vc-border); border-radius:10px;">
            <span>
                <span class="vc-badge-pill confirmada">Caja Abierta</span>
                {{ $cajaActual->nombre }} — apertura {{ $cajaActual->fecha_apertura->format('h:i A') }} · {{ $cajaActual->usuario->name }}
            </span>
            <button type="button" class="vc-btn vc-btn-danger-outline" id="btnCerrarCajaPos">
                <i class="fas fa-sign-out-alt"></i> Cerrar Caja
            </button>
        </div>
        <form id="formCerrarCajaPos" method="POST" action="{{ route('caja.cerrar', $cajaActual) }}" class="d-none">
            @csrf
            <input type="hidden" name="monto_cierre" value="">
        </form>
    @endif

    @if (!$cajaActual)
        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-3">
            <span><i class="fas fa-exclamation-triangle"></i> No tienes una caja abierta. Debes abrir caja antes de registrar ventas.</span>
            <button type="button" class="vc-btn vc-btn-success" data-toggle="modal" data-target="#modalAbrirCaja">Abrir Caja</button>
        </div>
    @endif

    <div class="vc-pos-layout">

        {{-- ===================== COLUMNA IZQUIERDA: catálogo ===================== --}}
        <div>
            <div class="vc-pos-tabs">
                <button type="button" class="is-active" data-tab="producto"><i class="fas fa-shopping-cart"></i> Productos</button>
                <button type="button" data-tab="servicio"><i class="fas fa-tools"></i> Servicios</button>
                <button type="button" data-tab="paquete"><i class="fas fa-box-open"></i> Paquetes</button>
            </div>

            <div class="vc-pos-search">
                <div class="box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscarProducto" placeholder="Buscar producto por nombre, código o escanear...">
                </div>
                <button type="button" class="scan-btn"><i class="fas fa-barcode"></i></button>
            </div>

            <div class="vc-pos-cats">
                <button type="button" class="is-active" data-cat="todos">Todos</button>
                @foreach ($categorias as $cat)
                    <button type="button" data-cat="{{ $cat->id }}">{{ $cat->nombre }}</button>
                @endforeach
            </div>

            <div class="vc-product-grid" id="gridProductos">
                @foreach ($productos as $producto)
                    <div class="vc-product-card {{ $producto->stock <= 0 ? 'is-agotado' : '' }}"
                         data-tipo="producto"
                         data-cat="{{ $producto->categoria_id }}"
                         data-nombre="{{ strtolower($producto->nombre) }}"
                         data-id="{{ $producto->id }}"
                         data-precio="{{ $producto->precio_venta }}"
                         data-iva="{{ $producto->iva_porcentaje }}"
                         data-stock="{{ $producto->stock }}"
                         data-nombre-display="{{ $producto->nombre }}"
                         data-sub="{{ $producto->descripcion ?? ($producto->categoria->nombre ?? '') }}"
                         data-icono="{{ $iconoPorProducto($producto) }}">
                        <div class="vc-product-card__img"><i class="fas {{ $iconoPorProducto($producto) }}" style="color: var(--vc-blue)"></i></div>
                        <div class="vc-product-card__name">{{ $producto->nombre }}</div>
                        <div class="vc-product-card__sub">{{ $producto->descripcion ?? ($producto->categoria->nombre ?? '') }}</div>
                        <div class="vc-product-card__price">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                        <div class="vc-product-card__stock">Stock: {{ $producto->stock }}</div>
                        <button type="button" class="vc-product-card__add"><i class="fas fa-plus"></i></button>
                    </div>
                @endforeach

                @foreach ($servicios as $producto)
                    <div class="vc-product-card d-none"
                         data-tipo="servicio"
                         data-cat="{{ $producto->categoria_id }}"
                         data-nombre="{{ strtolower($producto->nombre) }}"
                         data-id="{{ $producto->id }}"
                         data-precio="{{ $producto->precio_venta }}"
                         data-iva="{{ $producto->iva_porcentaje }}"
                         data-stock="999"
                         data-nombre-display="{{ $producto->nombre }}"
                         data-sub="Servicio"
                         data-icono="fa-stethoscope">
                        <div class="vc-product-card__img"><i class="fas fa-stethoscope" style="color: var(--vc-teal)"></i></div>
                        <div class="vc-product-card__name">{{ $producto->nombre }}</div>
                        <div class="vc-product-card__sub">Servicio</div>
                        <div class="vc-product-card__price">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                        <div class="vc-product-card__stock">&nbsp;</div>
                        <button type="button" class="vc-product-card__add"><i class="fas fa-plus"></i></button>
                    </div>
                @endforeach

                @foreach ($paquetes as $producto)
                    <div class="vc-product-card d-none"
                         data-tipo="paquete"
                         data-cat="{{ $producto->categoria_id }}"
                         data-nombre="{{ strtolower($producto->nombre) }}"
                         data-id="{{ $producto->id }}"
                         data-precio="{{ $producto->precio_venta }}"
                         data-iva="{{ $producto->iva_porcentaje }}"
                         data-stock="999"
                         data-nombre-display="{{ $producto->nombre }}"
                         data-sub="Paquete"
                         data-icono="fa-box-open">
                        <div class="vc-product-card__img"><i class="fas fa-box-open" style="color: var(--vc-purple)"></i></div>
                        <div class="vc-product-card__name">{{ $producto->nombre }}</div>
                        <div class="vc-product-card__sub">Paquete</div>
                        <div class="vc-product-card__price">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                        <div class="vc-product-card__stock">&nbsp;</div>
                        <button type="button" class="vc-product-card__add"><i class="fas fa-plus"></i></button>
                    </div>
                @endforeach

                @if ($productos->isEmpty())
                    <p class="text-muted small">Aún no tienes productos registrados en el inventario.</p>
                @endif
            </div>
        </div>

        {{-- ===================== COLUMNA DERECHA: carrito ===================== --}}
        <div class="vc-cart-panel">
            <div class="vc-cart-header">
                <span>Venta Actual (<span id="consecutivoVenta">{{ $siguienteConsecutivo }}</span>)</span>
                <button type="button" id="btnVaciarCarrito"><i class="fas fa-trash"></i></button>
            </div>

            <div class="vc-cart-body">
                <div class="vc-cart-items" id="carritoItems">
                    <div class="vc-cart-empty" id="carritoVacio">
                        <i class="fas fa-shopping-cart"></i>
                        Agrega productos desde el catálogo
                    </div>
                </div>

                <div class="vc-field mb-2">
                    <textarea id="observacionVenta" class="form-control" rows="2" placeholder="Agregar observación..."></textarea>
                </div>

                <div class="vc-cart-totals">
                    <div class="row"><span>Subtotal</span><strong id="txtSubtotal">$0</strong></div>
                    <div class="row">
                        <span>Descuento <input type="number" name="descuento_porcentaje" id="inputDescuento" min="0" max="100" value="0">%</span>
                        <strong id="txtDescuento">$0</strong>
                    </div>
                    <div class="row"><span>IVA (19%)</span><strong id="txtIva">$0</strong></div>
                    <div class="total-row">
                        <span class="label">TOTAL</span>
                        <span class="value" id="txtTotal">$0</span>
                    </div>
                </div>

                <div class="vc-payment-methods" id="metodosPago">
                    <button type="button" class="is-active" data-metodo="efectivo"><i class="fas fa-money-bill-wave"></i> Efectivo</button>
                    <button type="button" data-metodo="tarjeta"><i class="far fa-credit-card"></i> Tarjeta</button>
                    <button type="button" data-metodo="transferencia"><i class="fas fa-university"></i> Transferencia</button>
                    <button type="button" data-metodo="mixto"><i class="fas fa-random"></i> Mixto</button>
                </div>

                <div class="vc-field vc-pos-typeahead">
                    <label>Cliente (Opcional)</label>
                    <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar cliente por nombre, documento o teléfono...">
                    <input type="hidden" id="clienteIdSeleccionado">
                    <div class="vc-pos-typeahead-results" id="resultadosCliente"></div>
                </div>

                <div class="vc-field vc-pos-typeahead">
                    <label>Mascota (Opcional)</label>
                    <input type="text" id="buscarMascota" class="form-control" placeholder="Buscar mascota...">
                    <input type="hidden" id="mascotaIdSeleccionada">
                    <div class="vc-pos-typeahead-results" id="resultadosMascota"></div>
                </div>
            </div>

            <div class="vc-cart-footer">
                <button type="button" class="vc-btn-cobrar mb-2" style="width:100%" id="btnCobrar" {{ !$cajaActual ? 'disabled' : '' }}>
                    <i class="fas fa-money-bill"></i> Cobrar <span id="btnCobrarTotal">$0</span>
                </button>
                <button type="button" class="vc-btn-guardar" style="width:100%" id="btnGuardarVenta" {{ !$cajaActual ? 'disabled' : '' }}>
                    <i class="far fa-save"></i> Guardar Venta
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL ABRIR CAJA ===================== --}}
    <div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:16px;border:none;">
                <form action="{{ route('caja.abrir') }}" method="POST">
                    @csrf
                    <div class="modal-header" style="border-bottom:1px solid var(--vc-border)">
                        <h5 class="modal-title" style="font-weight:700;font-size:15px;"><i class="fas fa-cash-register"></i> Abrir Caja</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="vc-field">
                            <label>Nombre de la caja</label>
                            <input type="text" name="nombre" class="form-control" value="Caja Principal">
                        </div>
                        <div class="vc-field">
                            <label>Monto de apertura (efectivo inicial)</label>
                            <input type="number" name="monto_apertura" class="form-control" step="100" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid var(--vc-border)">
                        <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="vc-btn vc-btn-success"><i class="fas fa-check"></i> Abrir Caja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    // Datos que el backend expone al carrito JS (precio de venta, iva, stock)
    window.VC_POS_CONFIG = {
        rutaVentaStore: "{{ route('ventas.store') }}",
        rutaBuscarClientes: "{{ route('ventas.buscar-clientes') }}",
        rutaBuscarMascotas: "{{ route('ventas.buscar-mascotas') }}",
        csrfToken: "{{ csrf_token() }}",
        cajaAbierta: {{ $cajaActual ? 'true' : 'false' }},
    };
</script>
<script src="{{ asset('js/pos.js') }}"></script>
<script>
    document.getElementById('btnCerrarCajaPos')?.addEventListener('click', function () {
        var monto = prompt('Monto de cierre en caja (efectivo contado):');
        if (monto === null) return;
        if (!confirm('¿Confirmas el cierre de caja con $' + monto + '?')) return;
        var form = document.getElementById('formCerrarCajaPos');
        form.querySelector('[name=monto_cierre]').value = monto;
        form.submit();
    });
</script>
@endpush
