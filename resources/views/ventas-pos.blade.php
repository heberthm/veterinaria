@extends('layouts.app')


@section('subtitle', 'Ventas / POS')


@php
    // Crear alias $caja si solo existe $cajaActual
    if (!isset($caja) && isset($cajaActual)) {
        $caja = $cajaActual;
    }
    // Crear alias $cajaActual si solo existe $caja
    if (!isset($cajaActual) && isset($caja)) {
        $cajaActual = $caja;
    }
    // Valores por defecto
    $caja = $caja ?? null;
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
            <a href="{{ route('inicio') }}">Inicio</a> &rsaquo; <a href="#">Ventas</a>
        </div>
    </div>

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
                <input type="text" id="buscarProducto" placeholder="Buscar producto por nombre o código...">
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
                     data-sub="{{ $producto->descripcion ?? ($producto->categoria->nombre ?? '') }}">
                    <div class="vc-product-card__img"><i class="fas fa-capsules"></i></div>
                    <div class="vc-product-card__name">{{ $producto->nombre }}</div>
                    <div class="vc-product-card__sub">{{ $producto->descripcion ?? ($producto->categoria->nombre ?? '') }}</div>
                    <div class="vc-product-card__price">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                    <div class="vc-product-card__stock">Stock: {{ $producto->stock }}</div>
                    <button type="button" class="vc-product-card__add"><i class="fas fa-plus"></i></button>
                </div>
            @endforeach

            @foreach ($servicios as $producto)
                <div class="vc-product-card d-none"
                     data-tipo="servicio" data-cat="{{ $producto->categoria_id }}"
                     data-nombre="{{ strtolower($producto->nombre) }}" data-id="{{ $producto->id }}"
                     data-precio="{{ $producto->precio_venta }}" data-iva="{{ $producto->iva_porcentaje }}"
                     data-stock="999" data-nombre-display="{{ $producto->nombre }}" data-sub="Servicio">
                    <div class="vc-product-card__img"><i class="fas fa-stethoscope" style="color:var(--vc-teal)"></i></div>
                    <div class="vc-product-card__name">{{ $producto->nombre }}</div>
                    <div class="vc-product-card__sub">Servicio</div>
                    <div class="vc-product-card__price">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                    <div class="vc-product-card__stock">&nbsp;</div>
                    <button type="button" class="vc-product-card__add"><i class="fas fa-plus"></i></button>
                </div>
            @endforeach

            @foreach ($paquetes as $producto)
                <div class="vc-product-card d-none"
                     data-tipo="paquete" data-cat="{{ $producto->categoria_id }}"
                     data-nombre="{{ strtolower($producto->nombre) }}" data-id="{{ $producto->id }}"
                     data-precio="{{ $producto->precio_venta }}" data-iva="{{ $producto->iva_porcentaje }}"
                     data-stock="999" data-nombre-display="{{ $producto->nombre }}" data-sub="Paquete">
                    <div class="vc-product-card__img"><i class="fas fa-box-open" style="color:var(--vc-purple)"></i></div>
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
        <span>Factura venta No. <span id="consecutivoVenta">{{ $siguienteConsecutivo }}</span></span>
        <button type="button" id="btnVaciarCarrito"><i class="fas fa-trash"></i></button>
    </div>

    <div class="vc-cart-body">
        <div class="vc-cart-items" id="carritoItems">
            <div class="vc-cart-empty" id="carritoVacio">
                <i class="fas fa-shopping-cart"></i>
                Agrega productos desde el catálogo
            </div>
        </div>

        <div class="vc-cart-totals">
            <div class="row"><span>Subtotal</span><strong id="txtSubtotal">$0</strong></div>

            {{-- 🔥 IVA seleccionable --}}
           <div class="row vc-iva-row">
                <span class="vc-iva-label">
                    IVA
                    <select id="selectIva" class="vc-select-iva">
                        <option value="0">0%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                        <option value="16">16%</option>
                        <option value="19" selected>19%</option>
                    </select>
                </span>
                <strong id="txtIva">$0</strong>
            </div>  

            <div class="row">
                <span>Descuento <input type="number" id="inputDescuento" min="0" max="100" value="0">%</span>
                <strong id="txtDescuento">$0</strong>
            </div>

            <div class="total-row">
                <span class="label">TOTAL</span>
                <span class="value" id="txtTotal">$0</span>
            </div>
        </div>

        {{-- 🔥 Efectivo recibido + cambio --}}
        <div class="vc-efectivo-bloque">
            <label class="vc-efectivo-label">Efectivo Recibido</label>
            <input type="number" id="inputEfectivoRecibido" class="vc-input-efectivo"
                   min="0" step="100" value="0" placeholder="0">
            <div class="vc-cambio-barra" id="cambioBarra">
                <span>Cambio:</span>
                <strong id="txtCambio">$0</strong>
            </div>
        </div>

        {{-- 🔥 Métodos de pago --}}
        <div class="vc-payment-methods" id="metodosPago">
            <button type="button" class="is-active" data-metodo="efectivo"><i class="fas fa-money-bill-wave"></i> Efectivo</button>
            <button type="button" data-metodo="tarjeta"><i class="far fa-credit-card"></i> Tarjeta</button>
            <button type="button" data-metodo="transferencia"><i class="fas fa-university"></i> Transferencia</button>
            <button type="button" data-metodo="mixto"><i class="fas fa-random"></i> Mixto</button>
        </div>

        {{-- 🔥 Cliente (se queda) --}}
        <div class="vc-field vc-pos-typeahead">
            <label>Cliente (Opcional)</label>
            <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar cliente por nombre, documento o teléfono...">
            <input type="hidden" id="clienteIdSeleccionado">
            <div class="vc-pos-typeahead-results" id="resultadosCliente"></div>
        </div>

        {{-- 🔥 Tipo de comprobante --}}
        <div class="vc-tipo-comprobante">
            <label class="vc-tipo-comprobante__label">Tipo de Comprobante</label>
            <select id="tipoComprobante" class="vc-select">
                <option value="ticket">Ticket (80mm)</option>
                <option value="carta">Factura Carta</option>              
            </select>
        </div>

        {{-- 🔥 Botones de acción --}}
        <div class="vc-acciones-pos">
            <button type="button" class="vc-accion-btn vc-accion-btn--cancelar" id="btnCancelarVenta">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" class="vc-accion-btn vc-accion-btn--imprimir" id="btnImprimirVenta">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <button type="button" class="vc-accion-btn vc-accion-btn--cobrar" id="btnCobrarConImpresion">
                <i class="fas fa-check"></i> COBRAR
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL: PAGO EN EFECTIVO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPagoEfectivo" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-money-bill-wave"></i> Pago en Efectivo
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="vc-field">
                    <label>Total a cobrar</label>
                    <input type="text" id="totalEfectivo" class="form-control" readonly
                           style="background:#F3F4F6;font-weight:700;font-size:16px;color:#16A34A;">
                </div>
                <div class="vc-field">
                    <label>Monto recibido <span class="text-danger">*</span></label>
                    <input type="number" id="montoRecibidoEfectivo" class="form-control"
                           step="100" min="0" value="0" oninput="calcularCambioEfectivo()">
                </div>
                <div class="vc-field">
                    <label>Cambio</label>
                    <input type="text" id="cambioEfectivo" class="form-control" readonly
                           style="background:#F3F4F6;font-weight:700;font-size:16px;">
                </div>
                <div id="erroresPagoEfectivo" class="text-danger small mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                <button type="button" class="vc-btn vc-btn-primary" onclick="confirmarPagoEfectivo()"
                        style="background:#16A34A;color:#fff;">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>



{{-- ============================================================ --}}
{{-- 🔥 MODAL: PAGO CON TARJETA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPagoTarjeta" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-credit-card"></i> Pago con Tarjeta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formPagoTarjeta">
                    <div class="vc-field">
                        <label>Tipo de Tarjeta <span class="text-danger">*</span></label>
                        <select id="tipoTarjeta" class="form-control" required>
                            <option value="tarjeta_credito">Tarjeta de Crédito</option>
                            <option value="tarjeta_debito">Tarjeta de Débito</option>
                        </select>
                    </div>

                    <div class="vc-field">
                        <label>Últimos 4 dígitos <span class="text-danger">*</span></label>
                        <input type="text" id="ultimosDigitos" class="form-control" 
                               maxlength="4" pattern="\d{4}" 
                               placeholder="Ej: 1234" required>
                    </div>

                    <div class="vc-field">
                        <label>Número de Autorización <span class="text-danger">*</span></label>
                        <input type="text" id="numeroAutorizacion" class="form-control" 
                               placeholder="Número de aprobación del datáfono" required>
                    </div>

                    <div class="vc-field">
                        <label>Banco Emisor</label>
                        <input type="text" id="bancoEmisor" class="form-control" 
                               placeholder="Ej: Bancolombia, Davivienda...">
                    </div>

                    <div class="vc-field">
                        <label>Total a Cobrar</label>
                        <input type="text" id="totalTarjeta" class="form-control" readonly 
                               style="background:#F3F4F6;font-weight:700;font-size:16px;color:#2F6FED;">
                    </div>

                    <div id="erroresPagoTarjeta" class="text-danger small mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                <button type="button" class="vc-btn vc-btn-primary" onclick="confirmarPagoTarjeta()" 
                        style="background:#2F6FED;color:#fff;">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL: PAGO CON TRANSFERENCIA --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPagoTransferencia" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-university"></i> Pago por Transferencia
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formPagoTransferencia">
                    <div class="vc-field">
                        <label>Banco Destino <span class="text-danger">*</span></label>
                        <select id="bancoDestino" class="form-control" required>
                            <option value="">Seleccionar banco...</option>
                            <option value="Bancolombia">Bancolombia</option>
                            <option value="Davivienda">Davivienda</option>
                            <option value="BBVA">BBVA</option>
                            <option value="Banco de Bogotá">Banco de Bogotá</option>
                            <option value="Nequi">Nequi</option>
                            <option value="Daviplata">Daviplata</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="vc-field">
                        <label>Número de Comprobante <span class="text-danger">*</span></label>
                        <input type="text" id="numeroComprobante" class="form-control" 
                               placeholder="Número de referencia de la transferencia" required>
                    </div>

                    <div class="vc-field">
                        <label>Nombre del Remitente <span class="text-danger">*</span></label>
                        <input type="text" id="nombreRemitente" class="form-control" 
                               placeholder="Quién realizó la transferencia" required>
                    </div>

                    <div class="vc-field">
                        <label>Fecha de Transferencia</label>
                        <input type="date" id="fechaTransferencia" class="form-control" 
                               value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="vc-field">
                        <label>Total a Cobrar</label>
                        <input type="text" id="totalTransferencia" class="form-control" readonly 
                               style="background:#F3F4F6;font-weight:700;font-size:16px;color:#17A2B8;">
                    </div>

                    <div id="erroresPagoTransferencia" class="text-danger small mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                <button type="button" class="vc-btn vc-btn-primary" onclick="confirmarPagoTransferencia()" 
                        style="background:#17A2B8;color:#fff;">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL: PAGO MIXTO --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPagoMixto" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-random"></i> Pago Mixto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formPagoMixto">
                    <div style="background:#F8FAFC;padding:12px;border-radius:10px;margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:13px;color:#64748B;">Total a cobrar:</span>
                            <strong id="totalMixtoDisplay" style="font-size:18px;color:#F59E0B;">$0</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                            <span style="font-size:13px;color:#64748B;">Pendiente:</span>
                            <strong id="pendienteMixto" style="font-size:16px;color:#EF4444;">$0</strong>
                        </div>
                    </div>

                    <div class="vc-field">
                        <label>Monto en Efectivo <span class="text-danger">*</span></label>
                        <input type="number" id="montoEfectivoMixto" class="form-control" 
                               step="100" min="0" value="0" required
                               oninput="calcularPendienteMixto()">
                    </div>

                    <div class="vc-field">
                        <label>Monto en Tarjeta</label>
                        <input type="number" id="montoTarjetaMixto" class="form-control" 
                               step="100" min="0" value="0"
                               oninput="calcularPendienteMixto()">
                    </div>

                    <div class="vc-field">
                        <label>Monto en Transferencia</label>
                        <input type="number" id="montoTransferenciaMixto" class="form-control" 
                               step="100" min="0" value="0"
                               oninput="calcularPendienteMixto()">
                    </div>

                    <div class="vc-field">
                        <label>Referencia / Observación</label>
                        <input type="text" id="referenciaMixto" class="form-control" 
                               placeholder="Detalle adicional del pago">
                    </div>

                    <div id="erroresPagoMixto" class="text-danger small mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                <button type="button" class="vc-btn vc-btn-primary" onclick="confirmarPagoMixto()" 
                        style="background:#F59E0B;color:#fff;">
                    <i class="fas fa-check"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 🔥 MODAL: VISTA PREVIA DE IMPRESIÓN --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalVistaPrevia" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" style="font-weight:700;font-size:15px;">
                    <i class="fas fa-print"></i> Vista previa de impresión
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="background:#F1F4F9;padding:20px;">

                {{-- Selector de formato --}}
                <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons" style="width:100%;display:flex;">
                    <label class="btn btn-outline-dark active" style="flex:1;">
                        <input type="radio" name="formatoImpresion" value="ticket" checked>
                        <i class="fas fa-receipt"></i> Ticket 80mm
                    </label>
                    <label class="btn btn-outline-dark" style="flex:1;">
                        <input type="radio" name="formatoImpresion" value="carta">
                        <i class="fas fa-file-invoice"></i> Factura Carta
                    </label>
                </div>

                {{-- Contenedor de la vista previa --}}
                <div id="previewImpresion" style="
                    background:#fff;
                    margin:0 auto;
                    box-shadow:0 2px 12px rgba(0,0,0,.08);
                    padding:20px;
                    max-height:520px;
                    overflow-y:auto;
                    transition:width .2s;
                ">
                    {{-- Aquí se inyecta el HTML del ticket o la factura --}}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cerrar</button>
                <button type="button" class="vc-btn" onclick="imprimirFormatoActual()"
                        style="background:#0E1B30;color:#fff;">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>


@endsection

{{-- ===================== CSS embebido (sin depender de public/css) ===================== --}}
@push('css')
<style>
    :root {
        --vc-blue: #2F6FED; --vc-blue-soft: #E8F0FE;
        --vc-green: #16A34A; --vc-green-soft: #E7F8ED;
        --vc-purple: #8B5CF6; --vc-purple-soft: #F1EBFE;
        --vc-orange: #F59E0B; --vc-orange-soft: #FEF3E0;
        --vc-teal: #14B8A6; --vc-teal-soft: #E3F9F6;
        --vc-red: #EF4444; --vc-red-soft: #FDE9E9;
        --vc-border: #E7EBF3; --vc-text-dark: #1A2332;
        --vc-text-muted: #8996AC; --vc-text-soft: #64748B;
        --vc-radius-md: 12px; --vc-radius-lg: 16px;
        --vc-shadow: 0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06);
    }

    .vc-breadcrumb-row { font-size: 12px; color: var(--vc-text-muted); margin-bottom: 12px; }
    .vc-breadcrumb-row a { color: var(--vc-text-muted); }
    .vc-breadcrumb-row .current { color: var(--vc-text-dark); font-weight: 600; }

    .vc-alert {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 13.5px;
    }
    .vc-alert-light { background: #fff; border: 1px solid var(--vc-border); }
    .vc-alert-warning { background: #FEF3E0; border: 1px solid #F5D999; color: #8a5c07; }

    .vc-badge-pill { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 999px; }
    .vc-badge-pill.confirmada { background: var(--vc-green-soft); color: var(--vc-green); }

    .vc-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 15px; border-radius: 10px; font-size: 13px; font-weight: 700;
        border: 1px solid transparent; white-space: nowrap; cursor: pointer;
    }
    .vc-btn-success { background: var(--vc-green); color: #fff; }
    .vc-btn-light { background: #F5F7FB; border-color: var(--vc-border); color: var(--vc-text-dark); }
    .vc-btn-danger-outline { background: #fff; border-color: var(--vc-red-soft); color: var(--vc-red); }

    .vc-field { margin-bottom: 14px; }
    .vc-field label { font-size: 12px; font-weight: 700; color: var(--vc-text-soft); display: block; margin-bottom: 6px; }
    .vc-field .form-control { border: 1px solid var(--vc-border); border-radius: 9px; font-size: 13px; padding: 8px 12px; height: auto; }
    .vc-field .form-control:focus { border-color: var(--vc-blue); box-shadow: none; }

    .vc-modal-content { border-radius: 16px; border: none; }
    .vc-modal-header, .vc-modal-footer { border-color: var(--vc-border); }

    .vc-pos-layout { display: grid; grid-template-columns: 1fr 380px; gap: 18px; align-items: start; }

    .vc-pos-tabs { display: flex; gap: 8px; margin-bottom: 14px; }
    .vc-pos-tabs button {
        flex: 1; padding: 11px; border-radius: 10px; border: 1px solid var(--vc-border);
        background: #fff; font-size: 13px; font-weight: 700; color: var(--vc-text-soft);
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .vc-pos-tabs button.is-active { background: var(--vc-blue); color: #fff; border-color: var(--vc-blue); }

    .vc-pos-search { display: flex; gap: 10px; margin-bottom: 14px; }
    .vc-pos-search .box { flex: 1; position: relative; display: flex; align-items: center; }
    .vc-pos-search .box i { position: absolute; left: 14px; color: var(--vc-text-muted); font-size: 13px; }
    .vc-pos-search input { width: 100%; border: 1px solid var(--vc-border); border-radius: 10px; padding: 10px 14px 10px 38px; font-size: 13px; }
    .vc-pos-search .scan-btn { width: 42px; border: 1px solid var(--vc-border); border-radius: 10px; background: #fff; color: var(--vc-blue); }

    .vc-pos-cats { display: flex; gap: 8px; overflow-x: auto; margin-bottom: 16px; padding-bottom: 2px; }
    .vc-pos-cats button { white-space: nowrap; padding: 8px 16px; border-radius: 9px; border: 1px solid var(--vc-border); background: #fff; font-size: 12.5px; font-weight: 600; color: var(--vc-text-soft); }
    .vc-pos-cats button.is-active { background: var(--vc-blue); color: #fff; border-color: var(--vc-blue); }

    .vc-product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .vc-product-card { position: relative; background: #fff; border: 1px solid var(--vc-border); border-radius: var(--vc-radius-md); padding: 12px; cursor: pointer; }
    .vc-product-card:hover { box-shadow: var(--vc-shadow); }
    .vc-product-card__img { height: 92px; border-radius: 8px; background: #F1F4F9; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 30px; color: var(--vc-blue); }
    .vc-product-card__name { font-size: 12.5px; font-weight: 700; color: var(--vc-text-dark); line-height: 1.3; }
    .vc-product-card__sub { font-size: 11px; color: var(--vc-text-muted); margin-bottom: 6px; }
    .vc-product-card__price { font-size: 13.5px; font-weight: 800; color: var(--vc-text-dark); }
    .vc-product-card__stock { font-size: 10.5px; color: var(--vc-text-muted); }
    .vc-product-card__add { position: absolute; bottom: 10px; right: 10px; width: 26px; height: 26px; border-radius: 50%; border: none; background: var(--vc-green); color: #fff; }
    .vc-product-card.is-agotado { opacity: .5; pointer-events: none; }

    .vc-cart-panel { position: sticky; top: 20px; }
    .vc-cart-header { display: flex; align-items: center; justify-content: space-between; background: #0E1B30; color: #fff; padding: 14px 18px; border-radius: 16px 16px 0 0; font-size: 13.5px; font-weight: 700; }
    .vc-cart-header button { background: var(--vc-red); border: none; color: #fff; width: 30px; height: 30px; border-radius: 8px; }
    .vc-cart-body { background: #fff; border: 1px solid var(--vc-border); border-top: none; padding: 14px 18px; }
    .vc-cart-items {max-height:190px; overflow-y: auto; overflow-x: hidden; margin-bottom: 12px; padding-right: 4px; crollbar-width: thin; scrollbar-color: #C4CCDA transparent;}
    .vc-cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--vc-border); }
    .vc-cart-item__icon { width: 38px; height: 38px; border-radius: 8px; background: #F1F4F9; display: flex; align-items: center; justify-content: center; font-size: 15px; color: var(--vc-blue); flex-shrink: 0; }
    .vc-cart-item__info { flex: 1; min-width: 0; }
    .vc-cart-item__info strong { display: block; font-size: 12.5px; }
    .vc-cart-item__info span { font-size: 11px; color: var(--vc-text-muted); }
    .vc-cart-item__qty { display: flex; align-items: center; gap: 6px; }
    .vc-cart-item__qty button { width: 22px; height: 22px; border-radius: 6px; border: 1px solid var(--vc-border); background: #fff; font-size: 11px; }
    .vc-cart-item__qty span { font-size: 12.5px; font-weight: 700; min-width: 16px; text-align: center; }
    .vc-cart-item__subtotal { font-size: 12.5px; font-weight: 700; min-width: 68px; text-align: right; }
    .vc-cart-item__remove { background: none; border: none; color: var(--vc-text-muted); font-size: 13px; }
    .vc-cart-empty { text-align: center; padding: 30px 0; color: var(--vc-text-muted); font-size: 12.5px; }
    .vc-cart-empty i { font-size: 24px; display: block; margin-bottom: 8px; color: #C4CCDA; }

    .vc-cart-totals { font-size: 13px; }
    .vc-cart-totals .row { display: flex; justify-content: space-between; padding: 5px 0; color: var(--vc-text-soft); }
    .vc-cart-totals .row strong { color: var(--vc-text-dark); }
    .vc-cart-totals .total-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0 4px; border-top: 1px solid var(--vc-border); margin-top: 8px; }
    .vc-cart-totals .total-row .label { font-size: 14px; font-weight: 800; }
    .vc-cart-totals .total-row .value { font-size: 21px; font-weight: 800; color: var(--vc-blue); }
    .vc-cart-totals input#inputDescuento { width: 60px; border: 1px solid var(--vc-border); border-radius: 7px; padding: 3px 6px; font-size: 12px; text-align: center; }

    .vc-payment-methods { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 14px 0; }
    .vc-payment-methods button { display: flex; flex-direction: column; align-items: center; gap: 5px; padding: 10px 6px; border: 1.5px solid var(--vc-border); border-radius: 10px; background: #fff; font-size: 11.5px; font-weight: 600; color: var(--vc-text-soft); }
    .vc-payment-methods button i { font-size: 15px; }
    .vc-payment-methods button.is-active { border-color: var(--vc-blue); background: var(--vc-blue-soft); color: var(--vc-blue); }

    .vc-cart-footer { padding: 14px 18px 18px; background: #fff; border: 1px solid var(--vc-border); border-top: none; border-radius: 0 0 16px 16px; }
    .vc-btn-cobrar { background: var(--vc-green); color: #fff; padding: 13px; border-radius: 10px; font-weight: 800; font-size: 13.5px; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .vc-btn-cobrar:disabled, .vc-btn-guardar:disabled { opacity: .5; cursor: not-allowed; }
    .vc-btn-guardar { background: var(--vc-blue); color: #fff; padding: 13px; border-radius: 10px; font-weight: 800; font-size: 13.5px; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; }

    .vc-pos-typeahead { position: relative; }
    .vc-pos-typeahead-results {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 20;
        background: #fff; border: 1px solid var(--vc-border); border-radius: 10px; box-shadow: var(--vc-shadow);
        margin-top: 4px; max-height: 180px; overflow-y: auto; display: none;
    }
    .vc-pos-typeahead-results div { padding: 8px 12px; font-size: 12.5px; cursor: pointer; }
    .vc-pos-typeahead-results div:hover { background: #F5F7FB; }

    @media (max-width: 992px) {
        .vc-pos-layout { grid-template-columns: 1fr; }
        .vc-product-grid { grid-template-columns: repeat(2, 1fr); }
        .vc-cart-panel { position: static; }
    }

        /* ============================================================ */
        /* 🔥 IVA SELECCIONABLE                                          */
        /* ============================================================ */
            .vc-iva-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            color: #64748B;
            font-size: 13px;
        }

        .vc-iva-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748B;
        }

      .vc-select-iva {
    padding: 3px 22px 3px 8px;
    font-size: 12px;
    border: 1px solid #E7EBF3;
    border-radius: 7px;
    background-color: #fff;
    color: #1A2332;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 12px;
    text-align: center;
    min-width: 58px;
}
        /* ============================================================ */
        /* 🔥 EFECTIVO RECIBIDO + CAMBIO                                 */
        /* ============================================================ */
        .vc-efectivo-bloque {
            margin: 14px 0 4px;
        }

        .vc-efectivo-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1A2332;
            margin-bottom: 6px;
        }

        .vc-input-efectivo {
            width: 100%;
            padding: 11px 14px;
            font-size: 14px;
            border: 1px solid #E7EBF3;
            border-radius: 9px;
            background: #fff;
            color: #1A2332;
            margin-bottom: 8px;
        }

        .vc-input-efectivo:focus {
            outline: none;
            border-color: #2F6FED;
            box-shadow: 0 0 0 3px rgba(47,111,237,.12);
        }

        .vc-cambio-barra {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #16A34A;
            color: #fff;
            padding: 12px 16px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 14px;
            transition: background .2s;
        }

        .vc-cambio-barra.is-insuficiente {
            background: #DC3545;
        }

        .vc-cambio-barra strong {
            font-size: 15px;
            font-weight: 800;
        }

        /* ============================================================ */
        /* 🔥 TIPO DE COMPROBANTE + BOTONES                             */
        /* ============================================================ */
        .vc-tipo-comprobante {
            margin-top: 14px;
            margin-bottom: 12px;
        }

        .vc-tipo-comprobante__label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1A2332;
            margin-bottom: 6px;
        }

        .vc-select {
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            border: 1px solid #E7EBF3;
            border-radius: 9px;
            background: #fff;
            color: #1A2332;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
            cursor: pointer;
        }

        .vc-select:focus {
            outline: none;
            border-color: #2F6FED;
            box-shadow: 0 0 0 3px rgba(47,111,237,.12);
        }

        .vc-acciones-pos {
            display: grid;
            grid-template-columns: 1fr 1fr 1.1fr;
            gap: 8px;
        }

        .vc-accion-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 8px;
            font-size: 12.5px;
            font-weight: 800;
            color: #fff;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: transform .1s, filter .15s;
            white-space: nowrap;
        }

        .vc-accion-btn:hover  { filter: brightness(1.08); }
        .vc-accion-btn:active { transform: translateY(1px); }
        .vc-accion-btn i { font-size: 13px; }

        .vc-accion-btn--cancelar { background: #DC3545; }
        .vc-accion-btn--cancelar:hover { background: #C82333; }

        .vc-accion-btn--imprimir { background: #17A2B8; }
        .vc-accion-btn--imprimir:hover { background: #138496; }

        .vc-accion-btn--cobrar { background: #16A34A; font-weight: 900; letter-spacing: .3px; }
        .vc-accion-btn--cobrar:hover { background: #15803D; }

        .vc-accion-btn:disabled { opacity: .55; cursor: not-allowed; filter: none; }

            /* ============================================================ */
        /* 🔥 TIPO DE COMPROBANTE + BOTONES DE ACCIÓN                   */
        /* ============================================================ */
        .vc-tipo-comprobante {
            margin-top: 14px;
            margin-bottom: 12px;
        }

        .vc-tipo-comprobante__label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1A2332;
            margin-bottom: 6px;
        }

        .vc-select {
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            border: 1px solid #E7EBF3;
            border-radius: 9px;
            background: #fff;
            color: #1A2332;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
            cursor: pointer;
        }

        .vc-select:focus {
            outline: none;
            border-color: #2F6FED;
            box-shadow: 0 0 0 3px rgba(47, 111, 237, 0.12);
        }

        /* Contenedor de los 3 botones */
        .vc-acciones-pos {
            display: grid;
            grid-template-columns: 1fr 1fr 1.1fr;
            gap: 8px;
            margin-bottom: 14px;
        }

        .vc-accion-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 8px;
            font-size: 12.5px;
            font-weight: 800;
            color: #fff;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: transform .1s, filter .15s;
            white-space: nowrap;
        }

        .vc-accion-btn:hover  { filter: brightness(1.08); }
        .vc-accion-btn:active { transform: translateY(1px); }

        .vc-accion-btn i { font-size: 13px; }

        /* Cancelar */
        .vc-accion-btn--cancelar {
            background: #DC3545;
        }
        .vc-accion-btn--cancelar:hover { background: #C82333; }

        /* Imprimir */
        .vc-accion-btn--imprimir {
            background: #17A2B8;
        }
        .vc-accion-btn--imprimir:hover { background: #138496; }

        /* Cobrar */
        .vc-accion-btn--cobrar {
            background: #16A34A;
            font-weight: 900;
            letter-spacing: .3px;
        }
        .vc-accion-btn--cobrar:hover { background: #15803D; }

        .vc-accion-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            filter: none;
        }

        /* ============================================================ */
/* 🔥 PREVIEW EN MODAL — TICKET 80mm                            */
/* ============================================================ */
#previewImpresion.formato-ticket {
    width: 302px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #000;
    padding: 12px;
    margin: 0 auto;
}

#previewImpresion.formato-ticket .tk-center { text-align: center; }
#previewImpresion.formato-ticket .tk-bold   { font-weight: 700; }
#previewImpresion.formato-ticket .tk-small  { font-size: 10px; }

#previewImpresion.formato-ticket .tk-line {
    border-top: 1px dashed #000;
    margin: 6px 0;
}

#previewImpresion.formato-ticket .tk-items {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

#previewImpresion.formato-ticket .tk-item-name {
    font-weight: 700;
}

#previewImpresion.formato-ticket .tk-item-detail {
    display: flex;
    justify-content: space-between;
}

#previewImpresion.formato-ticket .tk-row {
    display: flex;
    justify-content: space-between;
}

#previewImpresion.formato-ticket .tk-total {
    font-weight: 700;
    font-size: 14px;
}

/* ============================================================ */
/* 🔥 PREVIEW EN MODAL — FACTURA CARTA                          */
/* ============================================================ */
/* Contenedor de la vista previa */
#previewImpresion {
    background: #fff;
    margin: 0 auto;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    padding: 20px;
    max-height: 520px;
    overflow-y: auto;
}

/* Ancho del ticket 80mm */
#previewImpresion.formato-ticket {
    width: 302px;
}

/* Ancho de la factura carta */
#previewImpresion.formato-carta {
    width: 100%;
    max-width: 720px;
}

</style>
@endpush

{{-- ===================== JS embebido (sin depender de public/js) ===================== --}}
@push('js')
<script>
(function () {
    'use strict';

    const $j = window.jQuery;

    const cfg = {
        rutaVentaStore: "{{ route('ventas.store') }}",
        rutaBuscarClientes: "{{ route('clientes.buscar') }}",
        rutaBuscarMascotas: "{{ route('ventas.buscar-mascotas') }}",
        rutaCajaAbrir: "{{ route('caja.abrir') }}",
        rutaCajaCerrar: "{{ route('caja.cerrar') }}",
        csrfToken: "{{ csrf_token() }}",
        cajaAbierta: {{ $caja->estaAbierta() ? 'true' : 'false' }},
        rutaVentaImprimir: "{{ route('ventas.imprimir', ['venta' => '__ID__']) }}",
    };

    const fmt = (n) => '$' + Math.round(n).toLocaleString('es-CO');
    let carrito = {};
    let clienteId = null;
    let mascotaId = null;
    let metodoPago = 'efectivo';
    let datosPago = null;
    let tipoComprobante = 'ticket';
    let ventaImpresion = null;
    let formatoActual = 'ticket';

    const $  = (sel) => document.querySelector(sel);
    const $$ = (sel) => Array.from(document.querySelectorAll(sel));

    // ============================================================
    // 🔥 TOTALES
    // ============================================================
    function calcularTotales() {
        const ivaPct = parseFloat(document.querySelector('#selectIva')?.value || 19) || 0;

        let subtotal = 0;
        Object.values(carrito).forEach((item) => {
            subtotal += item.precio * item.cantidad;
        });

        const iva = subtotal * (ivaPct / 100);
        const descuentoPct = parseFloat(document.querySelector('#inputDescuento')?.value || 0) || 0;
        const descuentoValor = subtotal * (descuentoPct / 100);
        const total = subtotal - descuentoValor + iva;

        return { subtotal, iva, descuentoValor, total, ivaPct };
    }

    // ============================================================
    // 🔥 CAMBIO
    // ============================================================
    function actualizarCambio() {
        const { total } = calcularTotales();
        const $recibido  = document.querySelector('#inputEfectivoRecibido');
        const $barra     = document.querySelector('#cambioBarra');
        const $txtCambio = document.querySelector('#txtCambio');

        if (!$recibido || !$barra || !$txtCambio) return;

        const recibido = parseFloat($recibido.value || 0) || 0;
        const cambio = recibido - total;

        if (cambio < 0) {
            $barra.classList.add('is-insuficiente');
            $txtCambio.textContent = 'Faltan ' + fmt(Math.abs(cambio));
        } else {
            $barra.classList.remove('is-insuficiente');
            $txtCambio.textContent = fmt(cambio);
        }
    }

    // ============================================================
    // 🔥 RENDER DEL CARRITO
    // ============================================================
    function renderCarrito() {
        const contenedor = document.querySelector('#carritoItems');
        if (!contenedor) {
            console.error('❌ No existe #carritoItems en el DOM');
            return;
        }

        const items = Object.values(carrito);

        // ---------- 1. Pintar items ----------
        contenedor.innerHTML = items.length === 0
            ? `<div class="vc-cart-empty"><i class="fas fa-shopping-cart"></i>Agrega productos desde el catálogo</div>`
            : items.map((item) => `
                <div class="vc-cart-item" data-id="${item.id}">
                    <div class="vc-cart-item__icon"><i class="fas fa-capsules"></i></div>
                    <div class="vc-cart-item__info">
                        <strong>${item.nombre}</strong>                      
                    </div>
                    <div class="vc-cart-item__qty">
                        <button type="button" class="btn-qty-menos" data-id="${item.id}">-</button>
                        <span>${item.cantidad}</span>
                        <button type="button" class="btn-qty-mas" data-id="${item.id}">+</button>
                    </div>
                    <div class="vc-cart-item__subtotal">${fmt(item.precio * item.cantidad)}</div>                   
                </div>
            `).join('');

        // ---------- 2. Totales ----------
        const { subtotal, iva, descuentoValor, total } = calcularTotales();
        const $subtotal = document.querySelector('#txtSubtotal');
        const $iva      = document.querySelector('#txtIva');
        const $desc     = document.querySelector('#txtDescuento');
        const $total    = document.querySelector('#txtTotal');

        if ($subtotal) $subtotal.textContent = fmt(subtotal);
        if ($iva)      $iva.textContent      = fmt(iva);
        if ($desc)     $desc.textContent     = fmt(descuentoValor);
        if ($total)    $total.textContent    = fmt(total);

        // ---------- 3. Cambio ----------
        actualizarCambio();

        // ---------- 4. Estado botón COBRAR ----------
        const $btnCobrar = document.querySelector('#btnCobrarConImpresion');
        if ($btnCobrar) $btnCobrar.disabled = items.length === 0;

        // ---------- 5. Scroll automático a partir del 3er item ----------
        const LIMITE_ITEMS = 3;
        if (items.length > LIMITE_ITEMS) {
            const itemsDOM = contenedor.querySelectorAll('.vc-cart-item');
            let alturaMax = 0;
            for (let i = 0; i < LIMITE_ITEMS; i++) {
                const el = itemsDOM[i];
                if (!el) continue;
                const rect = el.getBoundingClientRect();
                const est  = window.getComputedStyle(el);
                alturaMax += rect.height
                    + parseFloat(est.marginTop || 0)
                    + parseFloat(est.marginBottom || 0);
            }
            contenedor.style.maxHeight = alturaMax + 'px';
            contenedor.style.overflowY = 'auto';
            contenedor.scrollTop = contenedor.scrollHeight;
        } else {
            contenedor.style.maxHeight = '';
            contenedor.style.overflowY = '';
        }

        // ---------- 6. Re-enganchar listeners ----------
        document.querySelectorAll('.btn-qty-mas').forEach((b) => {
            b.addEventListener('click', () => cambiarCantidad(b.dataset.id, 1));
        });

        document.querySelectorAll('.btn-qty-menos').forEach((b) => {
            b.addEventListener('click', () => cambiarCantidad(b.dataset.id, -1));
        });

        document.querySelectorAll('.btn-quitar').forEach((b) => {
            b.addEventListener('click', () => {
                delete carrito[b.dataset.id];
                renderCarrito();
            });
        });
    }

    function cambiarCantidad(id, delta) {
        const item = carrito[id];
        if (!item) return;
        const nueva = item.cantidad + delta;
        if (nueva <= 0) delete carrito[id];
        else if (nueva > item.stock) alert(`Solo hay ${item.stock} unidades disponibles de "${item.nombre}".`);
        else item.cantidad = nueva;
        renderCarrito();
    }

    function agregarAlCarrito(card) {
        const id = card.dataset.id;
        const stock = parseInt(card.dataset.stock, 10);
        if (stock <= 0) { alert('Este producto no tiene stock disponible.'); return; }

        if (carrito[id]) {
            if (carrito[id].cantidad + 1 > stock) { alert(`Solo hay ${stock} unidades disponibles.`); return; }
            carrito[id].cantidad += 1;
        } else {
            carrito[id] = {
                id, nombre: card.dataset.nombreDisplay, sub: card.dataset.sub,
                precio: parseFloat(card.dataset.precio), iva: parseFloat(card.dataset.iva),
                cantidad: 1, stock,
            };
        }
        renderCarrito();
    }

    // ============================================================
    // 🔥 CATÁLOGO
    // ============================================================
    function filtrarCatalogo() {
        const tipoActivo = $('.vc-pos-tabs button.is-active')?.dataset.tab || 'producto';
        const catActiva = $('.vc-pos-cats button.is-active')?.dataset.cat || 'todos';
        const termino = ($('#buscarProducto')?.value || '').toLowerCase().trim();

        $$('.vc-product-card').forEach((card) => {
            const okTipo = card.dataset.tipo === tipoActivo;
            const okCat = catActiva === 'todos' || card.dataset.cat === catActiva;
            const okBusqueda = !termino || card.dataset.nombre.includes(termino);
            card.classList.toggle('d-none', !(okTipo && okCat && okBusqueda));
        });
    }

    // ============================================================
    // 🔥 TYPEAHEAD
    // ============================================================
    function inicializarTypeahead(inputSel, resultsSel, url, hiddenSel, onSelect) {
        const input = $(inputSel), results = $(resultsSel);
        let timeoutId = null;

        input?.addEventListener('input', () => {
            clearTimeout(timeoutId);
            const termino = input.value.trim();
            $(hiddenSel).value = '';
            if (termino.length < 2) { results.style.display = 'none'; return; }

            timeoutId = setTimeout(() => {
                let fetchUrl = `${url}?q=${encodeURIComponent(termino)}`;
                if (hiddenSel === '#mascotaIdSeleccionada' && clienteId) fetchUrl += `&cliente_id=${clienteId}`;

                fetch(fetchUrl).then((r) => r.json()).then((data) => {
                    if (data.length === 0) { results.style.display = 'none'; return; }
                    results.innerHTML = data.map((r) => `<div data-id="${r.id}" data-texto="${r.texto}">${r.texto}</div>`).join('');
                    results.style.display = 'block';
                    results.querySelectorAll('div').forEach((el) => {
                        el.addEventListener('click', () => {
                            input.value = el.dataset.texto;
                            $(hiddenSel).value = el.dataset.id;
                            results.style.display = 'none';
                            onSelect(el.dataset.id);
                        });
                    });
                });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!input?.contains(e.target) && !results?.contains(e.target)) results.style.display = 'none';
        });
    }

    // ============================================================
    // 🔥 PAGO — Funciones globales (usan jQuery)
    // ============================================================
    window.calcularCambioEfectivo = function () {
        const total = calcularTotales().total;
        const recibido = parseFloat($j('#montoRecibidoEfectivo').val()) || 0;
        const cambio = recibido - total;
        $j('#cambioEfectivo').val(cambio >= 0 ? fmt(cambio) : 'Insuficiente');
        $j('#cambioEfectivo').css('color', cambio >= 0 ? '#16A34A' : '#EF4444');
    };

    window.confirmarPagoEfectivo = function () {
        const total = calcularTotales().total;
        const recibido = parseFloat($j('#montoRecibidoEfectivo').val()) || 0;

        if (recibido < total) {
            $j('#erroresPagoEfectivo').html('El monto recibido es menor al total.');
            return;
        }

        datosPago = {
            metodo_pago: 'efectivo',
            referencia_pago: null,
            detalle_pago: { monto_recibido: recibido, cambio: recibido - total },
            monto_efectivo: total,
            monto_otro: 0,
            cambio: recibido - total,
        };

        $j('#modalPagoEfectivo').modal('hide');
        if (window.toastr) toastr.success('Pago en efectivo registrado');
    };

    window.calcularPendienteMixto = function () {
        const total = calcularTotales().total;
        const ef = parseFloat($j('#montoEfectivoMixto').val()) || 0;
        const tj = parseFloat($j('#montoTarjetaMixto').val()) || 0;
        const tr = parseFloat($j('#montoTransferenciaMixto').val()) || 0;
        const pendiente = total - (ef + tj + tr);

        $j('#pendienteMixto').text(fmt(pendiente));
        $j('#pendienteMixto').css('color', pendiente <= 0 ? '#22C55E' : '#EF4444');
    };

    window.confirmarPagoTarjeta = function () {
        const tipo = $j('#tipoTarjeta').val();
        const ultimos = ($j('#ultimosDigitos').val() || '').trim();
        const autorizacion = ($j('#numeroAutorizacion').val() || '').trim();
        const banco = ($j('#bancoEmisor').val() || '').trim();

        if (!/^\d{4}$/.test(ultimos)) {
            $j('#erroresPagoTarjeta').html('Ingrese los últimos 4 dígitos.'); return;
        }
        if (!autorizacion) {
            $j('#erroresPagoTarjeta').html('Ingrese el número de autorización.'); return;
        }

        const total = calcularTotales().total;
        datosPago = {
            metodo_pago: tipo,
            referencia_pago: autorizacion,
            detalle_pago: { tipo_tarjeta: tipo, ultimos_digitos: ultimos, numero_autorizacion: autorizacion, banco_emisor: banco },
            monto_efectivo: 0,
            monto_otro: total,
            cambio: 0,
        };

        $j('#modalPagoTarjeta').modal('hide');
        if (window.toastr) toastr.success('Datos de tarjeta guardados');
    };

    window.confirmarPagoTransferencia = function () {
        const banco = $j('#bancoDestino').val();
        const comprobante = ($j('#numeroComprobante').val() || '').trim();
        const remitente = ($j('#nombreRemitente').val() || '').trim();
        const fecha = $j('#fechaTransferencia').val();

        if (!banco) { $j('#erroresPagoTransferencia').html('Seleccione el banco.'); return; }
        if (!comprobante) { $j('#erroresPagoTransferencia').html('Ingrese el comprobante.'); return; }
        if (!remitente) { $j('#erroresPagoTransferencia').html('Ingrese el remitente.'); return; }

        const total = calcularTotales().total;
        datosPago = {
            metodo_pago: 'transferencia',
            referencia_pago: comprobante,
            detalle_pago: { banco_destino: banco, numero_comprobante: comprobante, nombre_remitente: remitente, fecha_transferencia: fecha },
            monto_efectivo: 0,
            monto_otro: total,
            cambio: 0,
        };

        $j('#modalPagoTransferencia').modal('hide');
        if (window.toastr) toastr.success('Datos de transferencia guardados');
    };

    window.confirmarPagoMixto = function () {
        const total = calcularTotales().total;
        const ef = parseFloat($j('#montoEfectivoMixto').val()) || 0;
        const tj = parseFloat($j('#montoTarjetaMixto').val()) || 0;
        const tr = parseFloat($j('#montoTransferenciaMixto').val()) || 0;
        const suma = ef + tj + tr;

        if (suma < total) {
            $j('#erroresPagoMixto').html('Faltan ' + fmt(total - suma) + ' por cubrir.');
            return;
        }

        datosPago = {
            metodo_pago: 'mixto',
            referencia_pago: $j('#referenciaMixto').val() || null,
            detalle_pago: { monto_efectivo: ef, monto_tarjeta: tj, monto_transferencia: tr, total_pagado: suma, cambio: Math.max(0, suma - total) },
            monto_efectivo: ef,
            monto_otro: tj + tr,
            cambio: Math.max(0, suma - total),
        };

        $j('#modalPagoMixto').modal('hide');
        if (window.toastr) toastr.success('Datos de pago mixto guardados');
    };

    // ============================================================
    // 🔥 ABRIR MODAL SEGÚN MÉTODO DE PAGO
    // ============================================================
    function abrirModalPago(metodo) {
        datosPago = null;
        const total = calcularTotales().total;

        if (metodo === 'efectivo') {
            $j('#totalEfectivo').val(fmt(total));
            $j('#montoRecibidoEfectivo').val(total);
            $j('#erroresPagoEfectivo').html('');
            window.calcularCambioEfectivo();
            $j('#modalPagoEfectivo').modal('show');
        }

        if (metodo === 'tarjeta') {
            $j('#totalTarjeta').val(fmt(total));
            $j('#erroresPagoTarjeta').empty();
            $j('#formPagoTarjeta')[0].reset();
            $j('#modalPagoTarjeta').modal('show');
        }

        if (metodo === 'transferencia') {
            $j('#totalTransferencia').val(fmt(total));
            $j('#erroresPagoTransferencia').empty();
            $j('#modalPagoTransferencia').modal('show');
        }

        if (metodo === 'mixto') {
            $j('#totalMixtoDisplay').text(fmt(total));
            $j('#montoEfectivoMixto').val(0);
            $j('#montoTarjetaMixto').val(0);
            $j('#montoTransferenciaMixto').val(0);
            $j('#pendienteMixto').text(fmt(total));
            $j('#erroresPagoMixto').empty();
            $j('#modalPagoMixto').modal('show');
        }
    }

    // ============================================================
    // 🔥 IMPRESIÓN
    // ============================================================
    const money = (n) => '$' + Number(n || 0).toLocaleString('es-CO');

    function fechaVenta(venta) {
        const f = venta.fecha || venta.created_at;
        if (!f) return '—';
        return new Date(f).toLocaleString('es-CO', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
    }

    function nombreCliente(venta) {
        if (!venta.cliente) return 'Consumidor final';
        return `${venta.cliente.nombres || ''} ${venta.cliente.apellidos || ''}`.trim() || 'Consumidor final';
    }

    function itemsVenta(venta) {
        return (venta.detalles || []).map((d) => {
            const nombre = d.nombre_producto || d.nombre || d.producto?.nombre || 'Producto';
            const cant   = Number(d.cantidad || 0);
            const precio = Number(d.precio_unitario ?? d.precio ?? 0);
            const iva    = Number(d.iva_porcentaje ?? d.iva ?? 0);
            const sub    = Number(d.subtotal ?? (cant * precio));
            return { nombre, cant, precio, iva, sub };
        });
    }

function htmlTicket(venta) {
    const items = itemsVenta(venta).map((it) => `
        <div class="tk-item">
            <div class="tk-item-name">${it.nombre}</div>
            <div class="tk-item-detail">
                <span>${it.cant} x ${money(it.precio)}</span>
                <span>${money(it.sub)}</span>
            </div>
        </div>
    `).join('');

    const num = venta.consecutivo || venta.numero_factura || venta.id;

    return `
        <style>
            .tk-wrap { font-family: 'Courier New', monospace; font-size: 12px; color: #000; line-height: 1.4; }
            .tk-wrap .tk-center { text-align: center; }
            .tk-wrap .tk-bold { font-weight: 700; }
            .tk-wrap .tk-small { font-size: 10px; }
            .tk-wrap .tk-line { border-top: 1px dashed #000; margin: 6px 0; }
            .tk-wrap .tk-items { display: flex; flex-direction: column; gap: 4px; }
            .tk-wrap .tk-item-name { font-weight: 700; }
            .tk-wrap .tk-item-detail { display: flex; justify-content: space-between; }
            .tk-wrap .tk-row { display: flex; justify-content: space-between; }
            .tk-wrap .tk-total { font-weight: 700; font-size: 14px; margin-top: 4px; }
        </style>

        <div class="tk-wrap">
            <div class="tk-center tk-bold" style="font-size:14px;">VETCLOUD</div>
            <div class="tk-center tk-small">
                NIT: 000.000.000-0<br>
                Tel: (000) 000-0000<br>
                ${venta.tenant?.direccion || 'Calle 00 #00-00'}
            </div>
            <div class="tk-line"></div>
            <div class="tk-center tk-bold">TICKET DE VENTA</div>
            <div class="tk-center">No. ${num}</div>
            <div class="tk-small tk-center">${fechaVenta(venta)}</div>
            <div class="tk-line"></div>

            <div class="tk-small">
                <div><strong>Cliente:</strong> ${nombreCliente(venta)}</div>
                ${venta.cliente?.numero_documento ? `<div><strong>Doc:</strong> ${venta.cliente.numero_documento}</div>` : ''}
                ${venta.usuario ? `<div><strong>Atendió:</strong> ${venta.usuario.name || venta.usuario.nombre || ''}</div>` : ''}
            </div>
            <div class="tk-line"></div>

            <div class="tk-items">${items}</div>
            <div class="tk-line"></div>

            <div class="tk-row"><span>Subtotal</span><span>${money(venta.subtotal)}</span></div>
            <div class="tk-row"><span>IVA</span><span>${money(venta.iva)}</span></div>
            ${Number(venta.descuento) ? `<div class="tk-row"><span>Descuento</span><span>-${money(venta.descuento)}</span></div>` : ''}
            <div class="tk-line"></div>
            <div class="tk-row tk-total"><span>TOTAL</span><span>${money(venta.total)}</span></div>
            <div class="tk-line"></div>

            <div class="tk-small">
                <div><strong>Método:</strong> ${(venta.metodo_pago || '').toUpperCase()}</div>
                ${venta.metodo_pago === 'efectivo' && venta.monto_efectivo ? `<div><strong>Recibido:</strong> ${money(venta.monto_efectivo)}</div>` : ''}
                ${venta.cambio ? `<div><strong>Cambio:</strong> ${money(venta.cambio)}</div>` : ''}
                ${venta.referencia_pago ? `<div><strong>Ref:</strong> ${venta.referencia_pago}</div>` : ''}
                <div><strong>Estado:</strong> ${(venta.estado || 'pagada').toUpperCase()}</div>
            </div>

            <div class="tk-line"></div>
            <div class="tk-center tk-small">
                ¡Gracias por su compra!<br>
                www.vetcloud.com
            </div>
        </div>
    `;
}

     
 function htmlFactura(venta) {
    const items = itemsVenta(venta).map((it) => `
        <tr>
            <td>${it.nombre}</td>
            <td class="fc-num">${it.cant}</td>
            <td class="fc-num">${money(it.precio)}</td>
            <td class="fc-num">${it.iva}%</td>
            <td class="fc-num">${money(it.sub)}</td>
        </tr>
    `).join('');

    const num = venta.consecutivo || venta.numero_factura || venta.id;

    return `
        <style>
            .fc-wrap { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000; }
            .fc-wrap .fc-header {
                display: flex; justify-content: space-between; align-items: flex-start;
                border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px;
            }
            .fc-wrap .fc-header h2 { margin: 0 0 6px; font-size: 24px; }
            .fc-wrap .fc-header p { margin: 3px 0; font-size: 11px; }
            .fc-wrap .fc-header-right { text-align: right; }
            .fc-wrap .fc-info {
                display: flex; justify-content: space-between; gap: 20px;
                margin-bottom: 18px; padding-bottom: 12px;
                border-bottom: 1px solid #ccc;
            }
            .fc-wrap .fc-info-col { flex: 1; }
            .fc-wrap .fc-info-col strong {
                display: block; font-size: 10px; color: #555;
                text-transform: uppercase; margin-bottom: 6px; letter-spacing: .5px;
            }
            .fc-wrap .fc-info-col p { margin: 2px 0; font-size: 11px; }
            .fc-wrap .fc-tabla { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
            .fc-wrap .fc-tabla th,
            .fc-wrap .fc-tabla td {
                border: 1px solid #ccc; padding: 8px 10px;
                text-align: left; font-size: 11px;
            }
            .fc-wrap .fc-tabla th { background: #0E1B30; color: #fff; font-weight: 700; }
            .fc-wrap .fc-num { text-align: right; }
            .fc-wrap .fc-totales { display: flex; justify-content: flex-end; }
            .fc-wrap .fc-totales table { min-width: 260px; font-size: 12px; }
            .fc-wrap .fc-totales td { padding: 5px 10px; }
            .fc-wrap .fc-total td {
                font-weight: 700; font-size: 15px;
                border-top: 2px solid #000; padding-top: 8px;
            }
            .fc-wrap .fc-footer {
                margin-top: 26px; text-align: center; font-size: 10px; color: #666;
                border-top: 1px solid #ddd; padding-top: 12px;
            }
        </style>

        <div class="fc-wrap">
            <div class="fc-header">
                <div class="fc-header-left">
                    <h2>VETCLOUD</h2>
                    <p>NIT: 000.000.000-0</p>
                    <p>${venta.tenant?.direccion || 'Calle 00 #00-00 · Ciudad'}</p>
                    <p>Tel: (000) 000-0000</p>
                </div>
                <div class="fc-header-right">
                    <h2>FACTURA</h2>
                    <p><strong>No.</strong> ${num}</p>
                    <p><strong>Fecha:</strong> ${fechaVenta(venta)}</p>
                    <p><strong>Estado:</strong> ${(venta.estado || 'pagada').toUpperCase()}</p>
                </div>
            </div>

            <div class="fc-info">
                <div class="fc-info-col">
                    <strong>Cliente</strong>
                    <p>${venta.cliente ? nombreCliente(venta) : 'Consumidor final'}</p>
                    ${venta.cliente?.numero_documento ? `<p>Doc: ${venta.cliente.numero_documento}</p>` : ''}
                    ${venta.cliente?.telefono ? `<p>Tel: ${venta.cliente.telefono}</p>` : ''}
                </div>
                <div class="fc-info-col">
                    <strong>Mascota</strong>
                    ${venta.mascota
                        ? `<p>${venta.mascota.nombre}</p><p>${venta.mascota.especie || ''} ${venta.mascota.raza || ''}</p>`
                        : '<p>—</p>'}
                </div>
                <div class="fc-info-col">
                    <strong>Pago</strong>
                    <p>Método: ${(venta.metodo_pago || '').toUpperCase()}</p>
                    ${venta.referencia_pago ? `<p>Ref: ${venta.referencia_pago}</p>` : ''}
                    ${venta.cambio ? `<p>Cambio: ${money(venta.cambio)}</p>` : ''}
                </div>
            </div>

            <table class="fc-tabla">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="fc-num">Cant.</th>
                        <th class="fc-num">Vr. Unit.</th>
                        <th class="fc-num">IVA</th>
                        <th class="fc-num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>${items}</tbody>
            </table>

            <div class="fc-totales">
                <table>
                    <tr><td>Subtotal</td><td class="fc-num">${money(venta.subtotal)}</td></tr>
                    <tr><td>IVA</td><td class="fc-num">${money(venta.iva)}</td></tr>
                    ${Number(venta.descuento) ? `<tr><td>Descuento</td><td class="fc-num">-${money(venta.descuento)}</td></tr>` : ''}
                    <tr class="fc-total"><td>TOTAL</td><td class="fc-num">${money(venta.total)}</td></tr>
                </table>
            </div>

            <div class="fc-footer">
                Gracias por preferirnos · Documento generado por VetCloud POS<br>
                ${venta.usuario ? `Atendido por: ${venta.usuario.name || venta.usuario.nombre || ''}` : ''}
            </div>
        </div>
    `;
}

    function aplicarFormato(formato) {
        formatoActual = formato;
        const cont = document.querySelector('#previewImpresion');
        if (!cont || !ventaImpresion) return;

        cont.classList.remove('formato-ticket', 'formato-carta');
        cont.classList.add(formato === 'ticket' ? 'formato-ticket' : 'formato-carta');
        cont.innerHTML = formato === 'ticket'
            ? htmlTicket(ventaImpresion)
            : htmlFactura(ventaImpresion);
    }

    document.addEventListener('change', (e) => {
        if (e.target.name === 'formatoImpresion') aplicarFormato(e.target.value);
    });

    function imprimirFormatoActual() {
        if (!ventaImpresion) return;

        const html = formatoActual === 'ticket'
            ? htmlTicket(ventaImpresion)
            : htmlFactura(ventaImpresion);

        // Estilos base para la ventana de impresión (los .tk-* y .fc-* ya van embebidos)
        const estilosBase = `
            @page { size: ${formatoActual === 'ticket' ? '80mm auto; margin: 0' : 'letter; margin: 12mm'}; }
            * { box-sizing: border-box; }
            body { margin: 0; }
            ${formatoActual === 'ticket'
                ? '.tk-wrap { width: 80mm; padding: 4mm 3mm; }'
                : '.fc-wrap { padding: 6mm; }'
            }
        `;

        const wrapper = document.createElement('div');
        wrapper.className = formatoActual === 'ticket' ? 'ticket-wrapper' : 'factura-wrapper';
        wrapper.innerHTML = html;

        const ventana = window.open('', '_blank', 'width=800,height=700');
        ventana.document.open();
        ventana.document.write(`
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>${formatoActual === 'ticket' ? 'Ticket' : 'Factura'} - ${ventaImpresion.consecutivo || ventaImpresion.id}</title>
                <style>${estilosBase}</style>
            </head>
            <body>
                ${wrapper.outerHTML}
            </body>
            </html>
        `);
        ventana.document.close();

        // 🔥 Esperar a que la ventana cargue, imprimir y cerrar
        ventana.onload = () => {
            ventana.focus();
            ventana.print();
        };

        // 🔥 Cuando la ventana de impresión se cierre (después de print o cancel),
        //    cerrar el modal del POS
        const cerrarModal = () => {
            if (window.jQuery) {
                window.jQuery('#modalVistaPrevia').modal('hide');
            }
        };

        // Chrome/Edge/Firefox: onafterprint se dispara después de imprimir/cancelar
        ventana.onafterprint = () => {
            cerrarModal();
            ventana.close();
        };

        // Fallback: detectar cierre de la ventana
        const checkCerrada = setInterval(() => {
            if (ventana.closed) {
                clearInterval(checkCerrada);
                cerrarModal();
            }
        }, 500);
    }

    async function abrirVistaPrevia(ventaId) {
        try {
            const url = cfg.rutaVentaImprimir.replace('__ID__', ventaId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'No se pudo cargar la venta.');

            ventaImpresion = data.venta;
            formatoActual = 'ticket';

            const radioTicket = document.querySelector('input[name="formatoImpresion"][value="ticket"]');
            if (radioTicket) radioTicket.checked = true;

            aplicarFormato('ticket');
            if (window.jQuery) window.jQuery('#modalVistaPrevia').modal('show');
        } catch (err) {
            alert('Error al cargar la venta: ' + err.message);
        }
    }

    // ============================================================
    // 🔥 REGISTRAR VENTA
    // ============================================================
    function registrarVenta() {
        if (Object.keys(carrito).length === 0) return;

        if (metodoPago !== 'efectivo' && !datosPago) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos de pago requeridos',
                text: 'Complete el formulario del método de pago antes de continuar.',
            });
            abrirModalPago(metodoPago);
            return;
        }
        if (metodoPago === 'efectivo' && !datosPago) {
            abrirModalPago('efectivo');
            return;
        }

        const { ivaPct } = calcularTotales();

        const payload = {
            items: Object.values(carrito).map((i) => ({
                producto_id: i.id,
                cantidad: i.cantidad,
                precio_unitario: i.precio,
                iva_porcentaje: ivaPct,
                subtotal: i.precio * i.cantidad,
            })),
            cliente_id: clienteId || null,
            descuento_porcentaje: parseFloat(document.querySelector('#inputDescuento').value || 0) || 0,
            metodo_pago: metodoPago,
            observacion: null,
            referencia_pago: datosPago?.referencia_pago || null,
            detalle_pago: datosPago?.detalle_pago || null,
            monto_efectivo: datosPago?.monto_efectivo ?? null,
            monto_otro: datosPago?.monto_otro ?? null,
            cambio: datosPago?.cambio ?? null,
        };

        document.querySelector('#btnCobrarConImpresion').disabled = true;

        fetch(cfg.rutaVentaStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'No se pudo registrar la venta.');
                return data;
            })
            .then((data) => {
                abrirVistaPrevia(data.venta.id);

                const formatoSel = document.querySelector('#tipoComprobante')?.value || 'ticket';
                setTimeout(() => {
                    if (formatoSel === 'carta' || formatoSel === 'ticket') {
                        aplicarFormato(formatoSel);
                        const radio = document.querySelector(`input[name="formatoImpresion"][value="${formatoSel}"]`);
                        if (radio) radio.checked = true;
                    }
                }, 400);

                if (window.jQuery) {
                    window.jQuery('#modalVistaPrevia').one('hidden.bs.modal', () => {
                        window.location.reload();
                    });
                } else {
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .catch((err) => {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                document.querySelector('#btnCobrarConImpresion').disabled = false;
                renderCarrito();
            });
    }

    // ============================================================
    // 🔥 BOTONES NUEVOS
    // ============================================================
    function cancelarVenta() {
        if (Object.keys(carrito).length === 0) {
            Swal.fire({ icon: 'info', title: 'Nada que cancelar', text: 'El carrito está vacío.', timer: 1500, showConfirmButton: false });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: '¿Cancelar venta?',
            html: `<p style="color:#64748B;font-size:14px;margin:0;">Se perderán <strong>todos los productos</strong> del carrito actual.</p>`,
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#6C757D',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, cancelar',
            cancelButtonText: '<i class="fas fa-times"></i> Volver',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (!result.isConfirmed) return;

            carrito = {};
            datosPago = null;
            clienteId = null;
            mascotaId = null;
            renderCarrito();

            const $cli = document.querySelector('#buscarCliente');
            const $desc = document.querySelector('#inputDescuento');
            const $cliId = document.querySelector('#clienteIdSeleccionado');
            if ($cli) $cli.value = '';
            if ($desc) $desc.value = 0;
            if ($cliId) $cliId.value = '';

            Swal.fire({ icon: 'success', title: 'Venta cancelada', timer: 1400, showConfirmButton: false });
        });
    }

    function imprimirVentaActual() {
        if (Object.keys(carrito).length === 0) {
            Swal.fire({ icon: 'info', title: 'Carrito vacío', text: 'Agrega productos antes de imprimir.', timer: 1500, showConfirmButton: false });
            return;
        }

        const { subtotal, iva, descuentoValor, total } = calcularTotales();
        const items = Object.values(carrito).map((it) => ({
            nombre: it.nombre,
            cant:   it.cantidad,
            precio: it.precio,
            iva:    it.iva,
            sub:    it.precio * it.cantidad,
        }));

        ventaImpresion = {
            id: 'PREVENTA',
            consecutivo: 'PREVENTA',
            fecha: new Date().toISOString(),
            subtotal, iva, descuento: descuentoValor, total,
            metodo_pago: metodoPago,
            estado: 'pendiente',
            observaciones: '',
            detalles: items,
            cliente: clienteId ? { nombres: document.querySelector('#buscarCliente')?.value || '', apellidos: '' } : null,
            mascota: null,
        };

        const formatoSel = document.querySelector('#tipoComprobante')?.value || 'ticket';
        const formatoVista = formatoSel === 'carta' ? 'carta' : 'ticket';
        aplicarFormato(formatoVista);

        if (window.jQuery) window.jQuery('#modalVistaPrevia').modal('show');
    }

    function cobrarConImpresion() {
        if (Object.keys(carrito).length === 0) {
            Swal.fire({ icon: 'info', title: 'Carrito vacío', text: 'Agrega productos antes de cobrar.', timer: 1500, showConfirmButton: false });
            return;
        }

        if (metodoPago !== 'efectivo' && !datosPago) {
            Swal.fire({ icon: 'warning', title: 'Datos de pago requeridos', text: 'Complete el formulario del método de pago antes de continuar.' });
            abrirModalPago(metodoPago);
            return;
        }
        if (metodoPago === 'efectivo' && !datosPago) {
            abrirModalPago('efectivo');
            return;
        }

        registrarVenta();
    }

    // ============================================================
    // 🔥 INIT
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        $$('.vc-product-card').forEach((card) => card.addEventListener('click', (e) => { e.preventDefault(); agregarAlCarrito(card); }));

        $$('.vc-pos-tabs button').forEach((btn) => btn.addEventListener('click', () => {
            $$('.vc-pos-tabs button').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            filtrarCatalogo();
        }));

        $$('.vc-pos-cats button').forEach((btn) => btn.addEventListener('click', () => {
            $$('.vc-pos-cats button').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            filtrarCatalogo();
        }));

        $('#buscarProducto')?.addEventListener('input', filtrarCatalogo);

        $$('#metodosPago button').forEach((btn) => btn.addEventListener('click', () => {
            $$('#metodosPago button').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            metodoPago = btn.dataset.metodo;
            datosPago = null;
            abrirModalPago(metodoPago);
        }));

        $('#inputDescuento')?.addEventListener('input', renderCarrito);
        document.querySelector('#inputEfectivoRecibido')?.addEventListener('input', actualizarCambio);
        document.querySelector('#selectIva')?.addEventListener('change', () => { renderCarrito(); });

        document.querySelector('#btnCancelarVenta')?.addEventListener('click', cancelarVenta);
        document.querySelector('#btnImprimirVenta')?.addEventListener('click', imprimirVentaActual);
        document.querySelector('#btnCobrarConImpresion')?.addEventListener('click', cobrarConImpresion);

        document.querySelector('#tipoComprobante')?.addEventListener('change', (e) => {
            tipoComprobante = e.target.value;
        });

        inicializarTypeahead('#buscarCliente', '#resultadosCliente', cfg.rutaBuscarClientes, '#clienteIdSeleccionado', (id) => { clienteId = id; });

        // Vaciar carrito (delegación)
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#btnVaciarCarrito');
            if (!btn) return;
            e.preventDefault();

            if (Object.keys(carrito).length === 0) {
                Swal.fire({ icon: 'info', title: 'Carrito vacío', text: 'No hay productos en el carrito.', timer: 2000, showConfirmButton: false });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: '¿Vaciar carrito?',
                html: `<p style="color:#64748B;font-size:14px;margin:0 0 8px;">Se eliminarán <strong>todos los productos</strong> del carrito actual.</p>
                       <p style="color:#EF4444;font-size:13px;margin:0;"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer</p>`,
                showCancelButton: true,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#6C757D',
                confirmButtonText: '<i class="fas fa-trash"></i> Sí, vaciar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (!result.isConfirmed) return;
                carrito = {};
                datosPago = null;
                renderCarrito();
                Swal.fire({ icon: 'success', title: '¡Carrito vaciado!', timer: 1800, showConfirmButton: false });
            });
        });

        $('#formAbrirCajaPos')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const form = new FormData(this);
            const errores = $('#erroresAbrirCaja');
            errores.innerHTML = '';

            fetch(cfg.rutaCajaAbrir, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' },
                body: form,
            })
                .then(async (res) => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(() => window.location.reload())
                .catch((data) => {
                    const lista = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'No se pudo abrir la caja.');
                    errores.innerHTML = lista;
                });
        });

        filtrarCatalogo();
        renderCarrito();
    });

    // Exponer globales necesarias
    window.imprimirFormatoActual = imprimirFormatoActual;
    window.abrirVistaPrevia = abrirVistaPrevia;
})();
</script>
@endpush
