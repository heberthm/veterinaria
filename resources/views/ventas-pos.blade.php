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

 // 🔥 ALIAS PARA COMPATIBILIDAD
    $caja = $cajaActual ?? $caja ?? null;
    
    // 🔥 VALORES POR DEFECTO
    $cajaActual = $cajaActual ?? null;
    $productos = $productos ?? collect([]);
    $servicios = $servicios ?? collect([]);
    $paquetes = $paquetes ?? collect([]);
    $categorias = $categorias ?? collect([]);
    $siguienteConsecutivo = $siguienteConsecutivo ?? '000001';

@endphp

@section('content_body')

<br>
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

  {{-- ============================================================ --}}
{{-- 🔥 INDICADOR DE CAJA (SOLO VISUAL - SIN ACCIONES) --}}
{{-- ============================================================ --}}

    @if ($cajaActual)
        <div class="vc-pos-caja-status vc-pos-caja-status--abierta">
            <div class="vc-pos-caja-status__left">
                <span class="vc-pos-caja-status__icon">
                    <i class="fas fa-cash-register"></i>
                </span>
                <div>
                    <strong>Caja Abierta</strong>
                    <small>
                        {{ $cajaActual->nombre }} · 
                        Apertura: {{ \Carbon\Carbon::parse($cajaActual->fecha_apertura)->format('h:i A') }}
                        @if($cajaActual->usuarioApertura)
                            · {{ $cajaActual->usuarioApertura->name }}
                        @endif
                    </small>
                </div>
            </div>
            <div class="vc-pos-caja-status__right">
                <span class="vc-pos-caja-status__saldo">
                    ${{ number_format($cajaActual->saldo_actual ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
    @else
        <div class="vc-pos-caja-status vc-pos-caja-status--cerrada">
            <div class="vc-pos-caja-status__left">
                <span class="vc-pos-caja-status__icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                <div>
                    <strong>No hay caja abierta</strong>
                    <small>Debe abrir caja antes de registrar ventas</small>
                </div>
            </div>
            <div class="vc-pos-caja-status__right">
                <a href="{{ route('caja') }}" class="vc-btn vc-btn-light">
                    <i class="fas fa-external-link-alt"></i> Ir a Caja
                </a>
            </div>
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

           {{-- 🔥 CATEGORÍAS - VERSIÓN MEJORADA --}}
<div class="vc-pos-cats" id="categoriasContainer">
    <button type="button" class="is-active" data-cat="todos">
        <i class="fas fa-th"></i> Todos
    </button>
    @foreach ($categorias as $cat)
        <button type="button" data-cat="{{ $cat->id }}">
            @if($cat->icono)
                <i class="fas {{ $cat->icono }}"></i>
            @endif
            {{ $cat->nombre }}
        </button>
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
                <button type="button" 
                        id="btnVaciarCarrito" 
                        class="vc-cart-clear-btn"
                        title="Borrar carrito de compra">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="vc-cart-body">
                {{-- Items del carrito --}}
                <div class="vc-cart-items" id="carritoItems">
                    <div class="vc-cart-empty" id="carritoVacio">
                        <i class="fas fa-shopping-cart"></i>
                        Agrega productos desde el catálogo
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="vc-field mb-2">
                    <textarea id="observacionVenta" class="form-control" rows="2" 
                            placeholder="Agregar observación..."></textarea>
                </div>

                {{-- Totales --}}
                <div class="vc-cart-totals">
                    <div class="row">
                        <span>Subtotal</span>
                        <strong id="txtSubtotal">$0</strong>
                    </div>
                    <div class="row">
                        <span>
                            Descuento 
                            <input type="number" name="descuento_porcentaje" id="inputDescuento" 
                                min="0" max="100" value="0">%
                        </span>
                        <strong id="txtDescuento">$0</strong>
                    </div>
                    <div class="row">
                        <span>IVA (19%)</span>
                        <strong id="txtIva">$0</strong>
                    </div>
                    <div class="total-row">
                        <span class="label">TOTAL</span>
                        <span class="value" id="txtTotal">$0</span>
                    </div>
                </div>

                {{-- Métodos de pago --}}
                <div class="vc-payment-methods" id="metodosPago">
                    <button type="button" class="is-active" data-metodo="efectivo">
                        <i class="fas fa-money-bill-wave"></i> Efectivo
                    </button>
                    <button type="button" data-metodo="tarjeta">
                        <i class="far fa-credit-card"></i> Tarjeta
                    </button>
                    <button type="button" data-metodo="transferencia">
                        <i class="fas fa-university"></i> Transferencia
                    </button>
                    <button type="button" data-metodo="mixto">
                        <i class="fas fa-random"></i> Mixto
                    </button>
                </div>

                {{-- Cliente --}}
                <div class="vc-field vc-pos-typeahead">
                    <label>Cliente (Opcional)</label>
                    <input type="text" id="buscarCliente" class="form-control" 
                        placeholder="Buscar cliente...">
                    <input type="hidden" id="clienteIdSeleccionado">
                    <div class="vc-pos-typeahead-results" id="resultadosCliente"></div>
                </div>

                {{-- Mascota --}}
                <div class="vc-field vc-pos-typeahead">
                    <label>Mascota (Opcional)</label>
                    <input type="text" id="buscarMascota" class="form-control" 
                        placeholder="Buscar mascota...">
                    <input type="hidden" id="mascotaIdSeleccionada">
                    <div class="vc-pos-typeahead-results" id="resultadosMascota"></div>
                </div>
            </div>

            <div class="vc-cart-footer">
                <button type="button" class="vc-btn-cobrar mb-2" style="width:100%" 
                        id="btnCobrar" {{ !$cajaActual ? 'disabled' : '' }}>
                    <i class="fas fa-money-bill"></i> Cobrar <span id="btnCobrarTotal">$0</span>
                </button>
                <button type="button" class="vc-btn-guardar" style="width:100%" 
                        id="btnGuardarVenta" {{ !$cajaActual ? 'disabled' : '' }}>
                    <i class="far fa-save"></i> Guardar Venta
                </button>
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
        cajaAbierta: {{ $cajaActual && $cajaActual->estaAbierta() ? 'true' : 'false' }},
    };
</script>
<script src="{{ asset('js/pos.js') }}"></script>

<script>

// ============================================================
// 🔥 INICIALIZAR TOOLTIPS DE BOOTSTRAP
// ============================================================
$(document).ready(function() {
    // Inicializar todos los tooltips
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        delay: { show: 300, hide: 100 },
        container: 'body'
    });
});


// ============================================================
// 🔥 VACIAR CARRITO CON SWEETALERT2
// ============================================================
$(document).off('click', '#btnVaciarCarrito').on('click', '#btnVaciarCarrito', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Botón vaciar carrito clickeado');
    
    // 🔥 VERIFICAR QUE EL CARRITO EXISTE
    if (typeof carrito === 'undefined') {
        console.warn('Variable carrito no definida');
        carrito = [];
    }
    
    // Si el carrito está vacío
    if (carrito.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Carrito vacío',
            text: 'No hay productos en el carrito.',
            confirmButtonColor: '#3B82F6',
            timer: 2000,
            timerProgressBar: true
        });
        return;
    }

    // 🔥 CONFIRMACIÓN CON SWEETALERT2
    Swal.fire({
        icon: 'warning',
        title: '¿Vaciar carrito?',
        html: `
            <p style="color: #64748B; font-size: 14px;">
                Se eliminarán <strong>todos los productos</strong> del carrito actual.
            </p>
            <p style="color: #EF4444; font-size: 13px; margin-top: 8px;">
                <i class="fas fa-exclamation-triangle"></i>
                Esta acción no se puede deshacer
            </p>
        `,
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#6C757D',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, vaciar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        reverseButtons: true,
        focusCancel: true
    }).then(function(result) {
        if (result.isConfirmed) {
            // 🔥 VACIAR EL CARRITO
            carrito = [];
            
            // 🔥 ACTUALIZAR LA UI (VARIAS FORMAS POR SI ACASO)
            if (typeof window.renderCarrito === 'function') {
                window.renderCarrito();
            }
            if (typeof window.actualizarCarrito === 'function') {
                window.actualizarCarrito();
            }
            if (typeof window.renderCart === 'function') {
                window.renderCart();
            }
            if (typeof window.actualizarTotales === 'function') {
                window.actualizarTotales();
            }
            
            // 🔥 LIMPIAR EL HTML DEL CARRITO DIRECTAMENTE
            $('#carritoItems').html(`
                <div class="vc-cart-empty" id="carritoVacio">
                    <i class="fas fa-shopping-cart"></i>
                    Agrega productos desde el catálogo
                </div>
            `);
            
            // 🔥 RESETEAR TOTALES
            $('#txtSubtotal').text('$0');
            $('#txtDescuento').text('$0');
            $('#txtIva').text('$0');
            $('#txtTotal').text('$0');
            $('#btnCobrarTotal').text('$0');
            
            // 🔥 NOTIFICACIÓN DE ÉXITO
            Swal.fire({
                icon: 'success',
                title: '¡Carrito vaciado!',
                text: 'Todos los productos han sido eliminados.',
                confirmButtonColor: '#22C55E',
                confirmButtonText: 'Aceptar',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
});

</script>

@endpush
