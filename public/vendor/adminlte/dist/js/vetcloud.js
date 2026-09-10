document.addEventListener('DOMContentLoaded', function () {

    // Toggle visual Día / Semana / Mes del mini-calendario del dashboard
    document.querySelectorAll('.vc-cal-toggle button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.vc-cal-toggle button').forEach(function (b) {
                b.classList.remove('is-active');
            });
            btn.classList.add('is-active');
            // La vista completa de "Día"/"Semana" vive en el módulo de Agenda;
            // aquí solo se refleja el estado visual del switch.
        });
    });

    // Sidebar responsive (< 992px)
    const sidebar = document.querySelector('.vc-sidebar');
    const toggleSidebarBtn = document.querySelector('[data-toggle-sidebar]');
    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });
    }
});
