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
            <span><h7>Factura venta No. </h7><span id="consecutivoVenta">{{ $siguienteConsecutivo }}</span></span>
            <button type="button" id="btnVaciarCarrito" title="Vaciar carrito de compra"><i class="fas fa-trash"></i></button>
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
                    <span>Descuento <input type="number" id="inputDescuento" min="0" max="100" value="0">%</span>
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
          <button type="button" class="vc-btn-cobrar mb-2" style="width:100%" id="btnCobrar" {{ (!$cajaActual || !$cajaActual->estaAbierta()) ? 'disabled' : '' }}> <span id="btnCobrarTotal">$0</span>
            <button type="button" class="vc-btn-guardar" style="width:100%" id="btnGuardarVenta" {{ !$cajaActual->estaAbierta() ? 'disabled' : '' }}>
                <i class="far fa-save"></i> Guardar Venta
            </button>
        </div>
    </div>
</div>

{{-- ===================== MODAL ABRIR CAJA ===================== --}}
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content vc-modal-content">
            <form id="formAbrirCajaPos">
                @csrf
                <input type="hidden" name="caja_id" value="{{ $caja->id }}">
                <div class="modal-header vc-modal-header">
                    <h5 class="modal-title"><i class="fas fa-cash-register"></i> Abrir Caja</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="vc-field">
                        <label>Saldo inicial (efectivo en caja)</label>
                        <input type="number" name="saldo_inicial" class="form-control" step="100" value="0" required>
                    </div>
                    <div class="vc-field">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                    <div id="erroresAbrirCaja" class="text-danger small"></div>
                </div>
                <div class="modal-footer vc-modal-footer">
                    <button type="button" class="vc-btn vc-btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="vc-btn vc-btn-success"><i class="fas fa-check"></i> Abrir Caja</button>
                </div>
            </form>
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
</style>
@endpush

{{-- ===================== JS embebido (sin depender de public/js) ===================== --}}
@push('js')
<script>
(function () {
    'use strict';

    // jQuery con alias propio para no chocar con el helper $ nativo
    const $j = window.jQuery;

    const cfg = {
        rutaVentaStore: "{{ route('ventas.store') }}",
        rutaBuscarClientes: "{{ route('clientes.buscar') }}",
        rutaBuscarMascotas: "{{ route('ventas.buscar-mascotas') }}",
        rutaCajaAbrir: "{{ route('caja.abrir') }}",
        rutaCajaCerrar: "{{ route('caja.cerrar') }}",
        csrfToken: "{{ csrf_token() }}",
        cajaAbierta: {{ $caja->estaAbierta() ? 'true' : 'false' }},
    };

    const fmt = (n) => '$' + Math.round(n).toLocaleString('es-CO');
    let carrito = {};
    let clienteId = null;
    let mascotaId = null;
    let metodoPago = 'efectivo';
    let datosPago = null;

    // Helpers nativos
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => Array.from(document.querySelectorAll(sel));

    // ---------------- Carrito ----------------
    function calcularTotales() {
        let subtotal = 0, iva = 0;
        Object.values(carrito).forEach((item) => {
            const sub = item.precio * item.cantidad;
            subtotal += sub;
            iva += sub * (item.iva / 100);
        });
        const descuentoPct = parseFloat($('#inputDescuento')?.value || 0) || 0;
        const descuentoValor = subtotal * (descuentoPct / 100);
        const total = subtotal - descuentoValor + iva;
        return { subtotal, iva, descuentoValor, total };
    }

  function renderCarrito() {
    const contenedor = document.querySelector('#carritoItems');
    if (!contenedor) {
        console.error('❌ #carritoItems no existe en el DOM');
        return;
    }

    const items = Object.values(carrito);
    console.log('🟢 renderCarrito items:', items.length, items);

    // ---------- 1. Render ----------
    try {
        contenedor.innerHTML = items.length === 0
            ? `<div class="vc-cart-empty"><i class="fas fa-shopping-cart"></i>Agrega productos desde el catálogo</div>`
            : items.map((item) => `
                <div class="vc-cart-item" data-id="${item.id}">
                    <div class="vc-cart-item__icon"><i class="fas fa-capsules"></i></div>
                    <div class="vc-cart-item__info">
                        <strong>${item.nombre}</strong>
                        <span>${item.sub || ''}</span>
                    </div>
                    <div class="vc-cart-item__qty">
                        <button type="button" class="btn-qty-menos" data-id="${item.id}">-</button>
                        <span>${item.cantidad}</span>
                        <button type="button" class="btn-qty-mas" data-id="${item.id}">+</button>
                    </div>
                    <div class="vc-cart-item__subtotal">${fmt(item.precio * item.cantidad)}</div>
                    <button type="button" class="vc-cart-item__remove btn-quitar" data-id="${item.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
    } catch (err) {
        console.error('❌ Error al pintar items:', err);
    }

    // ---------- 2. Totales (protegido) ----------
    try {
        const { subtotal, iva, descuentoValor, total } = calcularTotales();
        const $sub = document.querySelector('#txtSubtotal');
        const $iva = document.querySelector('#txtIva');
        const $desc = document.querySelector('#txtDescuento');
        const $tot = document.querySelector('#txtTotal');
        const $btnTotal = document.querySelector('#btnCobrarTotal');

        if ($sub) $sub.textContent = fmt(subtotal);
        if ($iva) $iva.textContent = fmt(iva);
        if ($desc) $desc.textContent = fmt(descuentoValor);
        if ($tot) $tot.textContent = fmt(total);
        if ($btnTotal) $btnTotal.textContent = fmt(total);
    } catch (err) {
        console.error('❌ Error al calcular totales:', err);
    }

    // ---------- 3. Botones cobrar/guardar ----------
    const sinItems = items.length === 0;
    const $cobrar = document.querySelector('#btnCobrar');
    const $guardar = document.querySelector('#btnGuardarVenta');
    if ($cobrar) $cobrar.disabled = sinItems || !cfg.cajaAbierta;
    if ($guardar) $guardar.disabled = sinItems || !cfg.cajaAbierta;

    // ---------- 4. Scroll a partir del 3er ítem ----------
    try {
        const LIMITE_ITEMS = 2;
        if (items.length > LIMITE_ITEMS) {
            const itemsDOM = contenedor.querySelectorAll('.vc-cart-item');
            let alturaMax = 0;
            for (let i = 0; i < LIMITE_ITEMS; i++) {
                const el = itemsDOM[i];
                if (!el) continue;
                const rect = el.getBoundingClientRect();
                const est = window.getComputedStyle(el);
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
    } catch (err) {
        console.error('❌ Error al ajustar scroll:', err);
    }

    // ---------- 5. Re-enganchar listeners ----------
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

    // ---------------- Catálogo ----------------
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

    // ---------------- Typeahead ----------------
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
    // 🔥 REGISTRAR VENTA
    // ============================================================
    function registrarVenta() {
        if (Object.keys(carrito).length === 0) return;

        if (metodoPago !== 'efectivo' && !datosPago) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'Datos de pago requeridos', text: 'Complete el formulario del método de pago antes de continuar.' });
            } else {
                alert('Complete el formulario del método de pago antes de continuar.');
            }
            abrirModalPago(metodoPago);
            return;
        }
        if (metodoPago === 'efectivo' && !datosPago) {
            abrirModalPago('efectivo');
            return;
        }

        const payload = {
            items: Object.values(carrito).map((i) => ({ producto_id: i.id, cantidad: i.cantidad })),
            cliente_id: clienteId || null,
            mascota_id: mascotaId || null,
            descuento_porcentaje: parseFloat($('#inputDescuento').value || 0) || 0,
            metodo_pago: metodoPago,
            observacion: $('#observacionVenta').value || null,
            referencia_pago: datosPago?.referencia_pago || null,
            detalle_pago: datosPago?.detalle_pago || null,
            monto_efectivo: datosPago?.monto_efectivo ?? null,
            monto_otro: datosPago?.monto_otro ?? null,
            cambio: datosPago?.cambio ?? null,
        };

        [$('#btnCobrar'), $('#btnGuardarVenta')].forEach((b) => b && (b.disabled = true));

        fetch(cfg.rutaVentaStore, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'No se pudo registrar la venta.');
                return data;
            })
            .then((data) => {
                alert(`Venta ${data.venta.consecutivo} registrada correctamente.`);
                window.location.reload();
            })
            .catch((err) => { alert(err.message); renderCarrito(); });
    }

    // ---------------- Init ----------------
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

        // 🔥 Abrir modal al hacer clic en método de pago
        $$('#metodosPago button').forEach((btn) => btn.addEventListener('click', () => {
            $$('#metodosPago button').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            metodoPago = btn.dataset.metodo;
            datosPago = null;
            abrirModalPago(metodoPago);
        }));

        $('#inputDescuento')?.addEventListener('input', renderCarrito);
        $('#btnCobrar')?.addEventListener('click', registrarVenta);
        $('#btnGuardarVenta')?.addEventListener('click', registrarVenta);

        inicializarTypeahead('#buscarCliente', '#resultadosCliente', cfg.rutaBuscarClientes, '#clienteIdSeleccionado', (id) => { clienteId = id; });
        inicializarTypeahead('#buscarMascota', '#resultadosMascota', cfg.rutaBuscarMascotas, '#mascotaIdSeleccionada', (id) => { mascotaId = id; });

        // ============================================================
        // 🔥 VACIAR CARRITO CON SWEETALERT2
        // ============================================================
        // Delegación: se engancha a document, funciona aunque el botón se re-renderice
        
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#btnVaciarCarrito');
            if (!btn) return;

            e.preventDefault();
            console.log('🟢 clic en vaciar carrito');   // 👈 DEBUG

            // Carrito vacío → avisar
            if (Object.keys(carrito).length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Carrito vacío',
                    text: 'No hay productos en el carrito.',
                    confirmButtonColor: '#3B82F6',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                return;
            }

            // Confirmación
            Swal.fire({
                icon: 'warning',
                title: '¿Vaciar carrito?',
                html: `
                    <p style="color:#64748B;font-size:14px;margin:0 0 8px;">
                        Se eliminarán <strong>todos los productos</strong> del carrito actual.
                    </p>
                    <p style="color:#EF4444;font-size:13px;margin:0;">
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
            }).then((result) => {
                if (!result.isConfirmed) return;

                // 🔥 Vaciar el carrito REAL del IIFE
                carrito = {};
                datosPago = null;
                renderCarrito();

                Swal.fire({
                    icon: 'success',
                    title: '¡Carrito vaciado!',
                    text: 'Todos los productos han sido eliminados.',
                    confirmButtonColor: '#22C55E',
                    timer: 1800,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
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

    
})();
</script>
@endpush