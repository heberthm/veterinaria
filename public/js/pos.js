(function () {
    'use strict';

    const cfg = window.VC_POS_CONFIG || {};
    const fmt = (n) => '$' + Math.round(n).toLocaleString('es-CO');

    /** Estado en memoria del carrito: { [productoId]: {id, nombre, sub, precio, iva, cantidad, stock, icono} } */
    let carrito = {};
    let clienteId = null;
    let mascotaId = null;
    let metodoPago = 'efectivo';

    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => Array.from(document.querySelectorAll(sel));

    // ---------------------------------------------------------------
    // Render del carrito
    // ---------------------------------------------------------------
    function calcularTotales() {
        let subtotal = 0;
        let iva = 0;

        Object.values(carrito).forEach((item) => {
            const sub = item.precio * item.cantidad;
            subtotal += sub;
            iva += sub * (item.iva / 100);
        });

        const descuentoPct = parseFloat($('#inputDescuento')?.value || 0) || 0;
        const descuentoValor = subtotal * (descuentoPct / 100);
        const total = subtotal - descuentoValor + iva;

        return { subtotal, iva, descuentoPct, descuentoValor, total };
    }

    function renderCarrito() {
        const contenedor = $('#carritoItems');
        const items = Object.values(carrito);

        if (items.length === 0) {
            contenedor.innerHTML = `
                <div class="vc-cart-empty" id="carritoVacio">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Agrega productos desde el catálogo
                </div>`;
        } else {
            contenedor.innerHTML = items.map((item) => `
                <div class="vc-cart-item" data-id="${item.id}">
                    <div class="vc-cart-item__icon"><i class="fa-solid ${item.icono}"></i></div>
                    <div class="vc-cart-item__info">
                        <strong>${item.nombre}</strong>
                        <span>${item.sub}</span>
                    </div>
                    <div class="vc-cart-item__qty">
                        <button type="button" class="btn-qty-menos" data-id="${item.id}">-</button>
                        <span>${item.cantidad}</span>
                        <button type="button" class="btn-qty-mas" data-id="${item.id}">+</button>
                    </div>
                    <div class="vc-cart-item__subtotal">${fmt(item.precio * item.cantidad)}</div>
                    <button type="button" class="vc-cart-item__remove btn-quitar" data-id="${item.id}"><i class="fa-solid fa-xmark"></i></button>
                </div>
            `).join('');
        }

        const { subtotal, iva, descuentoValor, total } = calcularTotales();
        $('#txtSubtotal').textContent = fmt(subtotal);
        $('#txtIva').textContent = fmt(iva);
        $('#txtDescuento').textContent = fmt(descuentoValor);
        $('#txtTotal').textContent = fmt(total);
        $('#btnCobrarTotal').textContent = fmt(total);

        const sinItems = items.length === 0;
        $('#btnCobrar').disabled = sinItems || !cfg.cajaAbierta;
        $('#btnGuardarVenta').disabled = sinItems || !cfg.cajaAbierta;

        bindCartItemEvents();
    }

    function bindCartItemEvents() {
        $$('.btn-qty-mas').forEach((btn) => btn.addEventListener('click', () => cambiarCantidad(btn.dataset.id, 1)));
        $$('.btn-qty-menos').forEach((btn) => btn.addEventListener('click', () => cambiarCantidad(btn.dataset.id, -1)));
        $$('.btn-quitar').forEach((btn) => btn.addEventListener('click', () => {
            delete carrito[btn.dataset.id];
            renderCarrito();
        }));
    }

    function cambiarCantidad(id, delta) {
        const item = carrito[id];
        if (!item) return;

        const nuevaCantidad = item.cantidad + delta;
        if (nuevaCantidad <= 0) {
            delete carrito[id];
        } else if (nuevaCantidad > item.stock) {
            alert(`Solo hay ${item.stock} unidades disponibles de "${item.nombre}".`);
        } else {
            item.cantidad = nuevaCantidad;
        }
        renderCarrito();
    }

    function agregarAlCarrito(card) {
        const id = card.dataset.id;
        const stock = parseInt(card.dataset.stock, 10);

        if (stock <= 0) {
            alert('Este producto no tiene stock disponible.');
            return;
        }

        if (carrito[id]) {
            if (carrito[id].cantidad + 1 > stock) {
                alert(`Solo hay ${stock} unidades disponibles.`);
                return;
            }
            carrito[id].cantidad += 1;
        } else {
            carrito[id] = {
                id,
                nombre: card.dataset.nombreDisplay,
                sub: card.dataset.sub,
                precio: parseFloat(card.dataset.precio),
                iva: parseFloat(card.dataset.iva),
                cantidad: 1,
                stock,
                icono: card.dataset.icono,
            };
        }
        renderCarrito();
    }

    // ---------------------------------------------------------------
    // Catálogo: tabs, categorías, buscador
    // ---------------------------------------------------------------
    function inicializarCatalogo() {
        $$('.vc-product-card').forEach((card) => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                agregarAlCarrito(card);
            });
        });

        $$('.vc-pos-tabs button').forEach((btn) => {
            btn.addEventListener('click', () => {
                $$('.vc-pos-tabs button').forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                filtrarCatalogo();
            });
        });

        $$('.vc-pos-cats button').forEach((btn) => {
            btn.addEventListener('click', () => {
                $$('.vc-pos-cats button').forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                filtrarCatalogo();
            });
        });

        $('#buscarProducto')?.addEventListener('input', filtrarCatalogo);
    }

    function filtrarCatalogo() {
        const tipoActivo = $('.vc-pos-tabs button.is-active')?.dataset.tab || 'producto';
        const catActiva = $('.vc-pos-cats button.is-active')?.dataset.cat || 'todos';
        const termino = ($('#buscarProducto')?.value || '').toLowerCase().trim();

        $$('.vc-product-card').forEach((card) => {
            const coincideTipo = card.dataset.tipo === tipoActivo;
            const coincideCat = catActiva === 'todos' || card.dataset.cat === catActiva;
            const coincideBusqueda = !termino || card.dataset.nombre.includes(termino);

            card.classList.toggle('d-none', !(coincideTipo && coincideCat && coincideBusqueda));
        });
    }

    // ---------------------------------------------------------------
    // Métodos de pago
    // ---------------------------------------------------------------
    function inicializarMetodosPago() {
        $$('#metodosPago button').forEach((btn) => {
            btn.addEventListener('click', () => {
                $$('#metodosPago button').forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                metodoPago = btn.dataset.metodo;
            });
        });
    }

    // ---------------------------------------------------------------
    // Typeahead de cliente / mascota
    // ---------------------------------------------------------------
    function inicializarTypeahead(inputSel, resultsSel, url, hiddenSel, onSelect) {
        const input = $(inputSel);
        const results = $(resultsSel);
        let timeoutId = null;

        input?.addEventListener('input', () => {
            clearTimeout(timeoutId);
            const termino = input.value.trim();
            $(hiddenSel).value = '';

            if (termino.length < 2) {
                results.style.display = 'none';
                return;
            }

            timeoutId = setTimeout(() => {
                let fetchUrl = `${url}?q=${encodeURIComponent(termino)}`;
                if (hiddenSel === '#mascotaIdSeleccionada' && clienteId) {
                    fetchUrl += `&cliente_id=${clienteId}`;
                }

                fetch(fetchUrl)
                    .then((r) => r.json())
                    .then((data) => {
                        if (data.length === 0) {
                            results.style.display = 'none';
                            return;
                        }
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
            if (!input?.contains(e.target) && !results?.contains(e.target)) {
                results.style.display = 'none';
            }
        });
    }

    // ---------------------------------------------------------------
    // Envío de la venta
    // ---------------------------------------------------------------
    function construirPayload() {
        return {
            items: Object.values(carrito).map((i) => ({ producto_id: i.id, cantidad: i.cantidad })),
            cliente_id: clienteId || null,
            mascota_id: mascotaId || null,
            descuento_porcentaje: parseFloat($('#inputDescuento').value || 0) || 0,
            metodo_pago: metodoPago,
            observacion: $('#observacionVenta').value || null,
        };
    }

    function registrarVenta() {
        if (Object.keys(carrito).length === 0) return;

        const payload = construirPayload();
        [$('#btnCobrar'), $('#btnGuardarVenta')].forEach((b) => b && (b.disabled = true));

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
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'No se pudo registrar la venta.');
                }
                return data;
            })
            .then((data) => {
                alert(`Venta ${data.venta.consecutivo} registrada correctamente.`);
                carrito = {};
                clienteId = null;
                mascotaId = null;
                $('#observacionVenta').value = '';
                $('#inputDescuento').value = 0;
                $('#buscarCliente').value = '';
                $('#buscarMascota').value = '';
                renderCarrito();
                window.location.reload(); // refresca stock disponible y el consecutivo
            })
            .catch((err) => {
                alert(err.message);
                renderCarrito();
            });
    }

    // ---------------------------------------------------------------
    // Init
    // ---------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        inicializarCatalogo();
        inicializarMetodosPago();
        filtrarCatalogo();
        renderCarrito();

        $('#inputDescuento')?.addEventListener('input', renderCarrito);
        $('#btnVaciarCarrito')?.addEventListener('click', () => {
            if (Object.keys(carrito).length && !confirm('¿Vaciar el carrito actual?')) return;
            carrito = {};
            renderCarrito();
        });

        $('#btnCobrar')?.addEventListener('click', registrarVenta);
        $('#btnGuardarVenta')?.addEventListener('click', registrarVenta);

        inicializarTypeahead('#buscarCliente', '#resultadosCliente', cfg.rutaBuscarClientes, '#clienteIdSeleccionado', (id) => { clienteId = id; });
        inicializarTypeahead('#buscarMascota', '#resultadosMascota', cfg.rutaBuscarMascotas, '#mascotaIdSeleccionada', (id) => { mascotaId = id; });
    });
})();
