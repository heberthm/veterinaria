/* =====================================================================
   VetFlow – Modo claro / oscuro
   Archivo: public/js/tema.js
   - Recuerda la elección (localStorage) y, si no hay, sigue al sistema.
   - Aplica el tema antes de pintar la página (sin parpadeo): cárgalo en
     <head> SIN defer.
   - Inserta solo el botón en la barra superior (o flotante si no hay).
   - Corrige elementos con colores claros escritos a mano en las vistas.
   API: window.VetTema.get() / set('dark'|'light') / toggle()
   Evento: document.addEventListener('vf:tema', e => e.detail.tema)
   ===================================================================== */
(function () {
    'use strict';

    var KEY  = 'vf-tema';
    var root = document.documentElement;

    function leer() {
        try { var v = localStorage.getItem(KEY); if (v === 'dark' || v === 'light') return v; } catch (e) {}
        return (window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    function guardar(v) { try { localStorage.setItem(KEY, v); } catch (e) {} }

    /* ---- Aplicar lo antes posible (evita el destello blanco) ---- */
    var actual = leer();
    root.setAttribute('data-theme', actual);

    /* ===================== Corrección de colores en línea ===================== */
    function parse(c) {
        var m = /rgba?\(\s*([\d.]+)[,\s]+([\d.]+)[,\s]+([\d.]+)(?:[,\s/]+([\d.]+%?))?\s*\)/.exec(c || '');
        if (!m) return null;
        var a = m[4] === undefined ? 1 : (String(m[4]).indexOf('%') > -1 ? parseFloat(m[4]) / 100 : parseFloat(m[4]));
        return { r: +m[1], g: +m[2], b: +m[3], a: a };
    }
    function lum(c) {
        function f(v) { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); }
        return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    }
    function sat(c) {
        var mx = Math.max(c.r, c.g, c.b), mn = Math.min(c.r, c.g, c.b);
        return mx === 0 ? 0 : (mx - mn) / mx;
    }
    function tono(c) {
        var r = c.r / 255, g = c.g / 255, b = c.b / 255, mx = Math.max(r, g, b), mn = Math.min(r, g, b), d = mx - mn, h = 0;
        if (d === 0) return 0;
        if (mx === r) h = ((g - b) / d) % 6; else if (mx === g) h = (b - r) / d + 2; else h = (r - g) / d + 4;
        h = Math.round(h * 60); return h < 0 ? h + 360 : h;
    }
    function esBoton(el) {
        return el.tagName === 'BUTTON' || el.classList.contains('btn') || el.getAttribute('role') === 'button' ||
               (el.tagName === 'A' && /btn|boton|action|accion/i.test(el.className));
    }
    function mezclar(c, hacia, t) {
        return 'rgb(' + Math.round(c.r + (hacia.r - c.r) * t) + ',' +
                        Math.round(c.g + (hacia.g - c.g) * t) + ',' +
                        Math.round(c.b + (hacia.b - c.b) * t) + ')';
    }
    var SALTAR = { SCRIPT: 1, STYLE: 1, LINK: 1, META: 1, HEAD: 1, TITLE: 1, IMG: 1, SVG: 1, CANVAS: 1, VIDEO: 1,
                   IFRAME: 1, PATH: 1, NOSCRIPT: 1, BR: 1, HR: 1, I: 0 };

    // ¿El fondo efectivo (subiendo por los ancestros) es oscuro?
    function fondoOscuro(el) {
        for (var n = el; n && n.nodeType === 1; n = n.parentElement) {
            var c = parse(getComputedStyle(n).backgroundColor);
            if (c && c.a > 0.5) return lum(c) < 0.25;
        }
        return true; // el body es oscuro en este tema
    }

    function corregir(raiz) {
        if (root.getAttribute('data-theme') !== 'dark') return;
        var lista = (raiz || document.body).querySelectorAll('*');
        // 1.er paso: fondos claros → fondo oscuro del mismo matiz
        for (var i = 0; i < lista.length; i++) {
            var el = lista[i];
            if (SALTAR[el.tagName] || el.classList.contains('vf-fix-bg') || el.classList.contains('vf-fix-amarillo') || el.closest('.no-tema, #modalVistaPrevia')) continue;
            var cs = getComputedStyle(el);
            var bg = parse(cs.backgroundColor);
            if (bg && bg.a > 0.5 && lum(bg) > 0.55) {
                var s = sat(bg);
                // Botones amarillos/ámbar pastel → amarillo intenso (visible en oscuro)
                var h = tono(bg);
                if (s >= 0.12 && h >= 35 && h <= 65 && esBoton(el)) {
                    el.classList.add('vf-fix-amarillo');
                    continue;
                }
                // blanco/gris → superficie; colores pastel → tono oscuro del mismo color
                var nuevo = s < 0.12 ? '#1e293b' : mezclar(bg, { r: 15, g: 23, b: 42 }, 0.78);
                el.style.setProperty('--vf-fix-bg', nuevo);
                el.classList.add('vf-fix-bg');
                el.setAttribute('data-vf-bg', s < 0.12 ? 'n' : 'c');
            }
        }
        // 2.º paso: texto oscuro sobre fondo oscuro → claro
        for (var j = 0; j < lista.length; j++) {
            var e2 = lista[j];
            if (SALTAR[e2.tagName] || e2.classList.contains('vf-fix-fg') || e2.classList.contains('vf-fix-amarillo') || e2.closest('.no-tema, #modalVistaPrevia')) continue;
            var tiene = false;
            for (var k = 0; k < e2.childNodes.length; k++) {
                var nd = e2.childNodes[k];
                if (nd.nodeType === 3 && nd.nodeValue.trim()) { tiene = true; break; }
            }
            if (!tiene) continue;
            var fg = parse(getComputedStyle(e2).color);
            if (fg && lum(fg) < 0.3 && fondoOscuro(e2)) {
                var claro = e2.getAttribute('data-vf-bg') === 'c'
                    ? mezclar(fg, { r: 255, g: 255, b: 255 }, 0.7)    // texto de badges de color
                    : (sat(fg) > 0.6 ? mezclar(fg, { r: 255, g: 255, b: 255 }, 0.55) : '#e2e8f0');
                e2.style.setProperty('--vf-fix-fg', claro);
                e2.classList.add('vf-fix-fg');
            }
        }
    }

    function limpiar() {
        var a = document.querySelectorAll('.vf-fix-bg, .vf-fix-fg, .vf-fix-amarillo');
        for (var i = 0; i < a.length; i++) {
            a[i].classList.remove('vf-fix-bg', 'vf-fix-fg', 'vf-fix-amarillo');
            a[i].style.removeProperty('--vf-fix-bg');
            a[i].style.removeProperty('--vf-fix-fg');
            a[i].removeAttribute('data-vf-bg');
        }
    }

    var pendiente = null;
    function programar(raiz) {
        clearTimeout(pendiente);
        pendiente = setTimeout(function () { corregir(raiz); }, 60);
    }

    /* ===================== Gráficas (Chart.js) ===================== */
    function ajustarGraficas() {
        if (!window.Chart) return;
        var oscuro = root.getAttribute('data-theme') === 'dark';
        try {
            Chart.defaults.color = oscuro ? '#cbd5e1' : '#666';
            Chart.defaults.borderColor = oscuro ? 'rgba(148,163,184,.18)' : 'rgba(0,0,0,.1)';
            var inst = Chart.instances || {};
            Object.keys(inst).forEach(function (k) { inst[k].update('none'); });
        } catch (e) {}
    }

    /* ===================== Botón ===================== */
    var boton = null;
    function pintarBoton() {
        if (!boton) return;
        var osc = root.getAttribute('data-theme') === 'dark';
        boton.innerHTML = osc ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        boton.title = osc ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
        boton.setAttribute('aria-label', boton.title);
        boton.setAttribute('aria-pressed', osc ? 'true' : 'false');
        // Sin Font Awesome el icono queda vacío: usar símbolo de texto
        setTimeout(function () {
            var ic = boton.querySelector('i');
            if (ic && ic.getBoundingClientRect().width < 4) boton.textContent = osc ? '\u2600' : '\u263E';
        }, 50);
    }

    function aplicar(v, guardarEleccion) {
        root.setAttribute('data-theme', v);
        if (guardarEleccion !== false) guardar(v);
        if (v === 'dark') { clearTimeout(pendiente); corregir(); } else limpiar();
        pintarBoton();
        ajustarGraficas();
        document.dispatchEvent(new CustomEvent('vf:tema', { detail: { tema: v } }));
    }

    function crearBoton() {
        if (document.getElementById('vfTemaBtn')) return;
        boton = document.createElement('button');
        boton.type = 'button';
        boton.id = 'vfTemaBtn';
        boton.className = 'vf-theme-toggle';
        boton.addEventListener('click', function () { VetTema.toggle(); });

        // AdminLTE: la lista de la derecha de la barra superior
        var lista = document.querySelector('.main-header .navbar-nav.ml-auto, .main-header .navbar-nav.navbar-right');
        if (!lista) {
            var todas = document.querySelectorAll('.main-header .navbar-nav');
            lista = todas.length ? todas[todas.length - 1] : null;
        }
        if (lista) {
            var li = document.createElement('li');
            li.className = 'nav-item';
            boton.classList.add('nav-link');
            li.appendChild(boton);
            lista.insertBefore(li, lista.firstChild);
        } else {
            boton.classList.add('vf-theme-floating');   // login u otras páginas sin barra
            document.body.appendChild(boton);
        }
        pintarBoton();
    }

    /* ===================== API pública ===================== */
    var VetTema = window.VetTema = {
        get: function () { return root.getAttribute('data-theme'); },
        set: function (v) { aplicar(v === 'dark' ? 'dark' : 'light'); },
        toggle: function () { aplicar(this.get() === 'dark' ? 'light' : 'dark'); }
    };

    /* ===================== Arranque ===================== */
    function iniciar() {
        crearBoton();
        if (root.getAttribute('data-theme') === 'dark') { corregir(); ajustarGraficas(); }

        // Contenido dinámico (AJAX, modales, listados)
        if (window.MutationObserver) {
            new MutationObserver(function (muts) {
                if (root.getAttribute('data-theme') !== 'dark') return;
                for (var i = 0; i < muts.length; i++) {
                    if (muts[i].addedNodes.length) { programar(); return; }
                }
            }).observe(document.body, { childList: true, subtree: true });
        }
        // Estilos que cambian al abrir un modal
        document.addEventListener('shown.bs.modal', function () { programar(); });
        if (window.jQuery) window.jQuery(document).on('shown.bs.modal', function () { programar(); });

        // Las gráficas se crean después: reintenta ajustarlas
        window.addEventListener('load', function () { ajustarGraficas(); programar(); });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();

    // Si el usuario no eligió, seguir cambios del sistema
    if (window.matchMedia) {
        var mq = matchMedia('(prefers-color-scheme: dark)');
        var fn = function (e) {
            var guardado = null;
            try { guardado = localStorage.getItem(KEY); } catch (x) {}
            if (!guardado) aplicar(e.matches ? 'dark' : 'light', false);
        };
        if (mq.addEventListener) mq.addEventListener('change', fn); else if (mq.addListener) mq.addListener(fn);
    }
    // Sincronizar entre pestañas
    window.addEventListener('storage', function (e) {
        if (e.key === KEY && (e.newValue === 'dark' || e.newValue === 'light')) aplicar(e.newValue, false);
    });
    // Imprimir siempre en claro
    window.addEventListener('beforeprint', function () { root.setAttribute('data-print-prev', root.getAttribute('data-theme')); root.setAttribute('data-theme', 'light'); });
    window.addEventListener('afterprint',  function () { var p = root.getAttribute('data-print-prev'); if (p) { root.setAttribute('data-theme', p); root.removeAttribute('data-print-prev'); } });
})();