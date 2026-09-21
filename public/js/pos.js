/**
 * ============================================================
 * VETCLOUD - POS (Punto de Venta)
 * JavaScript completo con soporte para métodos de pago
 * ============================================================
 */

// ============================================================
// 🔥 VARIABLES GLOBALES
// ============================================================
window.carrito = [];
window.clienteSeleccionado = null;
window.mascotaSeleccionada = null;
window.metodoPagoSeleccionado = 'efectivo';
window.datosPago = null;

// ============================================================
// 🔥 INICIALIZACIÓN
// ============================================================
$(document).ready(function() {
    console.log('=== POS CARGADO ===');
    console.log('Config:', window.VC_POS_CONFIG);

    // Inicializar Select2 si está disponible
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Inicializar Tooltips
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Cargar categorías y productos
    inicializarEventos();
    actualizarCarrito();
});

// ============================================================
// 🔥 INICIALIZAR TODOS LOS EVENTOS
// ============================================================
function inicializarEventos() {
    // Tabs de tipo (Productos / Servicios / Paquetes)
    $('.vc-pos-tabs button').on('click', function() {
        var tipo = $(this).data('tab');
        $('.vc-pos-tabs button').removeClass('is-active');
        $(this).addClass('is-active');
        filtrarProductosPorTipo(tipo);
    });

    // Categorías
    $('.vc-pos-cats button').on('click', function() {
        var cat = $(this).data('cat');
        $('.vc-pos-cats button').removeClass('is-active');
        $(this).addClass('is-active');
        filtrarProductosPorCategoria(cat);
    });

    // Búsqueda de productos
    $('#buscarProducto').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        buscarProductos(query);
    });

    // Agregar producto al carrito
    $(document).on('click', '.vc-product-card', function(e) {
        e.preventDefault();
        agregarAlCarrito($(this));
    });

    // Eliminar producto del carrito
    $(document).on('click', '.btn-eliminar-item', function(e) {
        e.preventDefault();
        var index = $(this).data('index');
        eliminarDelCarrito(index);
    });

    // Cambiar cantidad
    $(document).on('change', '.input-cantidad', function() {
        var index = $(this).data('index');
        var cantidad = parseFloat($(this).val()) || 1;
        cambiarCantidad(index, cantidad);
    });

    // Vaciar carrito
    $('#btnVaciarCarrito').off('click').on('click', function(e) {
        e.preventDefault();
        vaciarCarrito();
    });

    // Descuento
    $('#inputDescuento').on('change keyup', function() {
        calcularTotales();
    });

    // Métodos de pago
    $('#metodosPago button').off('click').on('click', function() {
        var metodo = $(this).data('metodo');
        seleccionarMetodoPago(metodo);
    });

    // Buscar cliente
    $('#buscarCliente').on('keyup', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            buscarClientes(query);
        } else {
            $('#resultadosCliente').empty().hide();
        }
    });

    // Buscar mascota
    $('#buscarMascota').on('keyup', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            buscarMascotas(query);
        } else {
            $('#resultadosMascota').empty().hide();
        }
    });

    // Cerrar resultados al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscarCliente, #resultadosCliente').length) {
            $('#resultadosCliente').empty().hide();
        }
        if (!$(e.target).closest('#buscarMascota, #resultadosMascota').length) {
            $('#resultadosMascota').empty().hide();
        }
    });

    // Botón cobrar
    $('#btnCobrar').on('click', function() {
        procesarVenta('cobrar');
    });

    // Botón guardar venta
    $('#btnGuardarVenta').on('click', function() {
        procesarVenta('guardar');
    });
}

// ============================================================
// 🔥 FILTRAR PRODUCTOS
// ============================================================
function filtrarProductosPorTipo(tipo) {
    $('.vc-product-card').each(function() {
        var tipoCard = $(this).data('tipo');
        if (tipoCard === tipo) {
            $(this).removeClass('d-none');
        } else {
            $(this).addClass('d-none');
        }
    });
}

function filtrarProductosPorCategoria(cat) {
    $('.vc-product-card').each(function() {
        var catCard = $(this).data('cat');
        if (cat === 'todos' || catCard == cat) {
            $(this).removeClass('d-none');
        } else {
            $(this).addClass('d-none');
        }
    });
}

function buscarProductos(query) {
    $('.vc-product-card').each(function() {
        var nombre = $(this).data('nombre') || '';
        if (nombre.includes(query)) {
            $(this).removeClass('d-none');
        } else {
            $(this).addClass('d-none');
        }
    });
}

// ============================================================
// 🔥 CARRITO DE COMPRAS
// ============================================================
function agregarAlCarrito($card) {
    var id = parseInt($card.data('id'));
    var nombre = $card.data('nombre-display') || $card.find('.vc-product-card__name').text();
    var precio = parseFloat($card.data('precio'));
    var stock = parseInt($card.data('stock'));
    var iva = parseFloat($card.data('iva')) || 19;
    var tipo = $card.data('tipo') || 'producto';

    // Verificar si ya existe
    var existente = window.carrito.find(function(item) {
        return item.id === id;
    });

    if (existente) {
        if (tipo === 'producto' && existente.cantidad >= stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock insuficiente',
                text: 'No hay más unidades disponibles de ' + nombre,
                timer: 2000
            });
            return;
        }
        existente.cantidad++;
    } else {
        window.carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            stock: stock,
            iva: iva,
            tipo: tipo
        });
    }

    actualizarCarrito();
    toastr.success(nombre + ' agregado al carrito');
}

function eliminarDelCarrito(index) {
    window.carrito.splice(index, 1);
    actualizarCarrito();
}

function cambiarCantidad(index, cantidad) {
    if (cantidad < 1) {
        eliminarDelCarrito(index);
        return;
    }
    var item = window.carrito[index];
    if (item.tipo === 'producto' && cantidad > item.stock) {
        toastr.warning('Solo hay ' + item.stock + ' unidades disponibles');
        cantidad = item.stock;
    }
    item.cantidad = cantidad;
    actualizarCarrito();
}

function vaciarCarrito() {
    if (window.carrito.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Carrito vacío',
            text: 'No hay productos en el carrito.',
            timer: 1500,
            showConfirmButton: false
        });
        return;
    }

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
            window.carrito = [];
            window.datosPago = null;
            actualizarCarrito();
            
            Swal.fire({
                icon: 'success',
                title: '¡Carrito vaciado!',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

function actualizarCarrito() {
    var container = $('#carritoItems');
    
    if (window.carrito.length === 0) {
        container.html(`
            <div class="vc-cart-empty" id="carritoVacio">
                <i class="fas fa-shopping-cart"></i>
                Agrega productos desde el catálogo
            </div>
        `);
        calcularTotales();
        return;
    }

    var html = '';
    window.carrito.forEach(function(item, index) {
        var subtotal = item.precio * item.cantidad;
        html += `
            <div class="vc-cart-item" data-index="${index}">
                <div class="vc-cart-item__info">
                    <div class="vc-cart-item__name">${item.nombre}</div>
                    <div class="vc-cart-item__price">$${formatNumber(item.precio)}</div>
                </div>
                <div class="vc-cart-item__qty">
                    <button type="button" class="btn-qty" onclick="cambiarCantidad(${index}, ${item.cantidad - 1})">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="input-cantidad" data-index="${index}" 
                           value="${item.cantidad}" min="1" max="${item.stock}">
                    <button type="button" class="btn-qty" onclick="cambiarCantidad(${index}, ${item.cantidad + 1})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="vc-cart-item__subtotal">$${formatNumber(subtotal)}</div>
                <button type="button" class="btn-eliminar-item" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    container.html(html);
    calcularTotales();
}

function calcularTotales() {
    var subtotal = 0;
    var iva = 0;

    window.carrito.forEach(function(item) {
        var subtotalItem = item.precio * item.cantidad;
        subtotal += subtotalItem;
        iva += subtotalItem * (item.iva / 100);
    });

    var descuentoPorcentaje = parseFloat($('#inputDescuento').val()) || 0;
    var descuento = subtotal * (descuentoPorcentaje / 100);
    var total = subtotal - descuento + iva;

    $('#txtSubtotal').text('$' + formatNumber(subtotal));
    $('#txtDescuento').text('$' + formatNumber(descuento));
    $('#txtIva').text('$' + formatNumber(iva));
    $('#txtTotal').text('$' + formatNumber(total));
    $('#btnCobrarTotal').text('$' + formatNumber(total));
}

// ============================================================
// 🔥 MÉTODOS DE PAGO
// ============================================================
function seleccionarMetodoPago(metodo) {
    $('#metodosPago button').removeClass('is-active');
    $('#metodosPago button[data-metodo="' + metodo + '"]').addClass('is-active');
    
    window.metodoPagoSeleccionado = metodo;
    window.datosPago = null;

    if (metodo === 'efectivo') {
        // No se necesita modal, se cobra directo
        return;
    }

    var total = obtenerTotalActual();

    if (metodo === 'tarjeta') {
        $('#totalTarjeta').val('$' + formatNumber(total));
        $('#erroresPagoTarjeta').empty();
        $('#modalPagoTarjeta').modal('show');
    }

    if (metodo === 'transferencia') {
        $('#totalTransferencia').val('$' + formatNumber(total));
        $('#erroresPagoTransferencia').empty();
        $('#modalPagoTransferencia').modal('show');
    }

    if (metodo === 'mixto') {
        $('#totalMixtoDisplay').text('$' + formatNumber(total));
        $('#montoEfectivoMixto').val(0);
        $('#montoTarjetaMixto').val(0);
        $('#montoTransferenciaMixto').val(0);
        $('#pendienteMixto').text('$' + formatNumber(total));
        $('#erroresPagoMixto').empty();
        $('#modalPagoMixto').modal('show');
    }
}

function obtenerTotalActual() {
    var totalTexto = $('#txtTotal').text().replace(/[^0-9]/g, '');
    return parseFloat(totalTexto) || 0;
}

// ============================================================
// 🔥 CONFIRMAR PAGO CON TARJETA
// ============================================================
function confirmarPagoTarjeta() {
    var tipo = $('#tipoTarjeta').val();
    var ultimos = $('#ultimosDigitos').val();
    var autorizacion = $('#numeroAutorizacion').val();
    var banco = $('#bancoEmisor').val();

    if (!ultimos || ultimos.length !== 4) {
        $('#erroresPagoTarjeta').html('Ingrese los últimos 4 dígitos de la tarjeta.');
        return;
    }
    if (!autorizacion) {
        $('#erroresPagoTarjeta').html('Ingrese el número de autorización.');
        return;
    }

    window.datosPago = {
        metodo_pago: tipo,
        referencia_pago: autorizacion,
        detalle_pago: {
            tipo_tarjeta: tipo,
            ultimos_digitos: ultimos,
            numero_autorizacion: autorizacion,
            banco_emisor: banco
        }
    };

    $('#modalPagoTarjeta').modal('hide');
    toastr.success('Datos de tarjeta guardados correctamente');
}

// ============================================================
// 🔥 CONFIRMAR PAGO CON TRANSFERENCIA
// ============================================================
function confirmarPagoTransferencia() {
    var banco = $('#bancoDestino').val();
    var comprobante = $('#numeroComprobante').val();
    var remitente = $('#nombreRemitente').val();
    var fecha = $('#fechaTransferencia').val();

    if (!banco) {
        $('#erroresPagoTransferencia').html('Seleccione el banco destino.');
        return;
    }
    if (!comprobante) {
        $('#erroresPagoTransferencia').html('Ingrese el número de comprobante.');
        return;
    }
    if (!remitente) {
        $('#erroresPagoTransferencia').html('Ingrese el nombre del remitente.');
        return;
    }

    window.datosPago = {
        metodo_pago: 'transferencia',
        referencia_pago: comprobante,
        detalle_pago: {
            banco_destino: banco,
            numero_comprobante: comprobante,
            nombre_remitente: remitente,
            fecha_transferencia: fecha
        }
    };

    $('#modalPagoTransferencia').modal('hide');
    toastr.success('Datos de transferencia guardados correctamente');
}

// ============================================================
// 🔥 PAGO MIXTO
// ============================================================
function calcularPendienteMixto() {
    var total = obtenerTotalActual();
    var efectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
    var tarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
    var transferencia = parseFloat($('#montoTransferenciaMixto').val()) || 0;

    var suma = efectivo + tarjeta + transferencia;
    var pendiente = total - suma;

    $('#pendienteMixto').text('$' + formatNumber(pendiente));

    if (pendiente <= 0) {
        $('#pendienteMixto').css('color', '#22C55E');
    } else {
        $('#pendienteMixto').css('color', '#EF4444');
    }
}

function confirmarPagoMixto() {
    var total = obtenerTotalActual();
    var efectivo = parseFloat($('#montoEfectivoMixto').val()) || 0;
    var tarjeta = parseFloat($('#montoTarjetaMixto').val()) || 0;
    var transferencia = parseFloat($('#montoTransferenciaMixto').val()) || 0;

    var suma = efectivo + tarjeta + transferencia;

    if (suma < total) {
        $('#erroresPagoMixto').html(
            'Faltan $' + formatNumber(total - suma) + ' por cubrir.'
        );
        return;
    }

    window.datosPago = {
        metodo_pago: 'mixto',
        referencia_pago: $('#referenciaMixto').val(),
        detalle_pago: {
            monto_efectivo: efectivo,
            monto_tarjeta: tarjeta,
            monto_transferencia: transferencia,
            total_pagado: suma,
            cambio: Math.max(0, suma - total)
        },
        monto_efectivo: efectivo,
        monto_otro: tarjeta + transferencia,
        cambio: Math.max(0, suma - total)
    };

    $('#modalPagoMixto').modal('hide');
    toastr.success('Datos de pago mixto guardados correctamente');
}

// ============================================================
// 🔥 BUSCAR CLIENTES
// ============================================================
function buscarClientes(query) {
    $.ajax({
        url: window.VC_POS_CONFIG.rutaBuscarClientes,
        method: 'GET',
        data: { q: query },
        success: function(data) {
            var container = $('#resultadosCliente');
            container.empty();

            if (data.length === 0) {
                container.html('<div class="resultado-item">No se encontraron clientes</div>').show();
                return;
            }

            data.forEach(function(cliente) {
                container.append(`
                    <div class="resultado-item" onclick="seleccionarCliente(${cliente.id}, '${escapeHtml(cliente.nombres + ' ' + cliente.apellidos)}', '${escapeHtml(cliente.numero_documento || '')}')">
                        <strong>${escapeHtml(cliente.nombres + ' ' + cliente.apellidos)}</strong>
                        <small>${escapeHtml(cliente.numero_documento || '')}</small>
                    </div>
                `);
            });
            container.show();
        },
        error: function() {
            console.error('Error al buscar clientes');
        }
    });
}

function seleccionarCliente(id, nombre, documento) {
    window.clienteSeleccionado = { id: id, nombre: nombre, documento: documento };
    $('#buscarCliente').val(nombre + (documento ? ' - ' + documento : ''));
    $('#clienteIdSeleccionado').val(id);
    $('#resultadosCliente').empty().hide();
    toastr.success('Cliente seleccionado: ' + nombre);
}

// ============================================================
// 🔥 BUSCAR MASCOTAS
// ============================================================
function buscarMascotas(query) {
    $.ajax({
        url: window.VC_POS_CONFIG.rutaBuscarMascotas,
        method: 'GET',
        data: {
            q: query,
            cliente_id: window.clienteSeleccionado ? window.clienteSeleccionado.id : null
        },
        success: function(data) {
            var container = $('#resultadosMascota');
            container.empty();

            if (data.length === 0) {
                container.html('<div class="resultado-item">No se encontraron mascotas</div>').show();
                return;
            }

            data.forEach(function(mascota) {
                container.append(`
                    <div class="resultado-item" onclick="seleccionarMascota(${mascota.id}, '${escapeHtml(mascota.nombre)}')">
                        <strong>${escapeHtml(mascota.nombre)}</strong>
                        <small>${escapeHtml(mascota.especie || '')} ${mascota.raza ? '· ' + escapeHtml(mascota.raza) : ''}</small>
                    </div>
                `);
            });
            container.show();
        },
        error: function() {
            console.error('Error al buscar mascotas');
        }
    });
}

function seleccionarMascota(id, nombre) {
    window.mascotaSeleccionada = { id: id, nombre: nombre };
    $('#buscarMascota').val(nombre);
    $('#mascotaIdSeleccionada').val(id);
    $('#resultadosMascota').empty().hide();
    toastr.success('Mascota seleccionada: ' + nombre);
}

// ============================================================
// 🔥 PROCESAR VENTA
// ============================================================
function procesarVenta(accion) {
    // Validaciones
    if (window.carrito.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            text: 'Agrega al menos un producto antes de cobrar.',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    if (!window.VC_POS_CONFIG.cajaAbierta) {
        Swal.fire({
            icon: 'warning',
            title: 'No hay caja abierta',
            text: 'Debe abrir la caja antes de registrar ventas.',
            confirmButtonText: 'Ir a Caja',
            showCancelButton: true,
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '/caja';
            }
        });
        return;
    }

    // Validar datos de pago
    if (window.metodoPagoSeleccionado !== 'efectivo' && !window.datosPago) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos de pago requeridos',
            text: 'Complete el formulario del método de pago ' + window.metodoPagoSeleccionado + ' antes de continuar.',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    var total = obtenerTotalActual();
    var titulo = accion === 'cobrar' ? '¿Confirmar cobro?' : '¿Guardar venta?';

    Swal.fire({
        icon: 'question',
        title: titulo,
        html: `
            <div style="text-align:left;">
                <p><strong>Productos:</strong> ${window.carrito.length}</p>
                <p><strong>Método de pago:</strong> ${capitalize(window.metodoPagoSeleccionado)}</p>
                <p><strong>Total:</strong> <span style="color:#22C55E;font-size:20px;font-weight:700;">$${formatNumber(total)}</span></p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6C757D',
        confirmButtonText: 'Sí, ' + (accion === 'cobrar' ? 'cobrar' : 'guardar'),
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            enviarVenta(accion);
        }
    });
}

function enviarVenta(accion) {
    var productos = window.carrito.map(function(item) {
        return {
            id: item.id,
            cantidad: item.cantidad,
            precio: item.precio,
            iva: item.iva
        };
    });

    var data = {
        _token: window.VC_POS_CONFIG.csrfToken,
        productos: productos,
        cliente_id: window.clienteSeleccionado ? window.clienteSeleccionado.id : null,
        mascota_id: window.mascotaSeleccionada ? window.mascotaSeleccionada.id : null,
        metodo_pago: window.metodoPagoSeleccionado,
        descuento_porcentaje: parseFloat($('#inputDescuento').val()) || 0,
        observaciones: $('#observacionVenta').val(),
        accion: accion
    };

    // 🔥 AGREGAR DATOS DEL PAGO
    if (window.datosPago) {
        data.referencia_pago = window.datosPago.referencia_pago || null;
        data.detalle_pago = window.datosPago.detalle_pago || null;
        data.monto_efectivo = window.datosPago.monto_efectivo || null;
        data.monto_otro = window.datosPago.monto_otro || null;
        data.cambio = window.datosPago.cambio || null;
    }

    $.ajax({
        url: window.VC_POS_CONFIG.rutaVentaStore,
        method: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        beforeSend: function() {
            Swal.fire({
                title: 'Procesando venta...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Venta registrada!',
                    html: `
                        <p>Venta #<strong>${response.venta.numero_factura || response.venta.id}</strong> guardada exitosamente.</p>
                        <p style="font-size:18px;color:#22C55E;font-weight:700;">
                            Total: $${formatNumber(response.venta.total)}
                        </p>
                    `,
                    confirmButtonColor: '#22C55E',
                    showCancelButton: true,
                    confirmButtonText: 'Ver Factura',
                    cancelButtonText: 'Cerrar'
                }).then(function(result) {
                    if (result.isConfirmed && response.venta.id) {
                        window.open('/ventas/' + response.venta.id + '/pdf', '_blank');
                    }
                    // Limpiar y recargar
                    limpiarPOS();
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo procesar la venta.'
                });
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            var mensaje = 'Error al procesar la venta.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                mensaje = '';
                $.each(xhr.responseJSON.errors, function(key, value) {
                    mensaje += value[0] + '<br>';
                });
            }
            Swal.fire({
                icon: 'error',
                title: 'Error (' + xhr.status + ')',
                html: mensaje
            });
        }
    });
}

function limpiarPOS() {
    window.carrito = [];
    window.clienteSeleccionado = null;
    window.mascotaSeleccionada = null;
    window.datosPago = null;
    actualizarCarrito();
    $('#buscarCliente').val('');
    $('#buscarMascota').val('');
    $('#observacionVenta').val('');
    $('#inputDescuento').val(0);
}

// ============================================================
// 🔥 UTILIDADES
// ============================================================
function formatNumber(number) {
    return Number(number).toLocaleString('es-CO');
}

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ============================================================
// 🔥 EXPORTAR FUNCIONES GLOBALES
// ============================================================
window.agregarAlCarrito = agregarAlCarrito;
window.eliminarDelCarrito = eliminarDelCarrito;
window.cambiarCantidad = cambiarCantidad;
window.actualizarCarrito = actualizarCarrito;
window.calcularTotales = calcularTotales;
window.vaciarCarrito = vaciarCarrito;
window.seleccionarCliente = seleccionarCliente;
window.seleccionarMascota = seleccionarMascota;
window.confirmarPagoTarjeta = confirmarPagoTarjeta;
window.confirmarPagoTransferencia = confirmarPagoTransferencia;
window.confirmarPagoMixto = confirmarPagoMixto;
window.calcularPendienteMixto = calcularPendienteMixto;