<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\AplicacionMascotaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VacunacionController;
use App\Http\Controllers\DesparasitacionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

Auth::routes();

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Redirección Raíz
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('inicio');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
      Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');   
Route::get('/dashboard/citas-eventos', [DashboardController::class, 'eventos'])->name('dashboard.citas.eventos');


    /*
    |--------------------------------------------------------------------------
    | Agenda (Vista de calendario)
    |--------------------------------------------------------------------------
    */
  
  Route::get('agenda', [AgendaController::class, 'index'])->name('agenda'); 
  Route::get('agenda/datos', [AgendaController::class, 'datos'])->name('agenda.datos');
  Route::get('agenda/eventos', [AgendaController::class, 'eventos'])->name('agenda.eventos');
  Route::post('agenda/citas', [AgendaController::class, 'store'])->name('agenda.citas.store');
  Route::put('agenda/citas/{cita}', [AgendaController::class, 'update'])->name('agenda.citas.update');
  Route::patch('agenda/citas/{cita}/estado', [AgendaController::class, 'cambiarEstado'])->name('agenda.citas.estado');
  Route::patch('agenda/citas/{id}/mover', [AgendaController::class, 'mover'])->name('agenda.citas.mover');
  Route::delete('agenda/citas/{cita}', [AgendaController::class, 'destroy'])->name('agenda.citas.destroy');


    /*
    |--------------------------------------------------------------------------
    | Citas - CRUD con modales
    |--------------------------------------------------------------------------
    */
    Route::get('/citas', [CitaController::class, 'index'])->name('citas');
    Route::get('/citas/datatable', [CitaController::class, 'datatable'])->name('citas.datatable');
    Route::get('/citas/{id}', [CitaController::class, 'show'])->name('citas.show');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::put('/citas/{id}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('/citas/{id}', [CitaController::class, 'destroy'])->name('citas.destroy');
    Route::get('/citas/eventos', [CitaController::class, 'getEvents'])->name('citas.eventos');
    Route::put('/citas/{id}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    Route::get('/citas/{id}/detalle', [CitaController::class, 'detalle'])->name('citas.detalle');

    /*
    |--------------------------------------------------------------------------
    | Clientes - CRUD con modales
    |--------------------------------------------------------------------------
    */
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes');
    Route::get('/clientes/datatable', [ClienteController::class, 'datatable'])->name('clientes.datatable');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes/{id}/mascotas', [ClienteController::class, 'mascotas'])->name('clientes.mascotas');
    Route::get('/clientes/{id}/detalle', [ClienteController::class, 'detalle'])->name('clientes.detalle');

    /*
    |--------------------------------------------------------------------------
    | Mascotas - CRUD con modales
    |--------------------------------------------------------------------------
    */
    Route::get('/mascotas', [MascotaController::class, 'index'])->name('mascotas');
    Route::get('/mascotas/datatable', [MascotaController::class, 'datatable'])->name('mascotas.datatable');
    Route::post('/mascotas', [MascotaController::class, 'store'])->name('mascotas.store');
     Route::put('/mascotas/{id}', [MascotaController::class, 'update'])->name('mascotas.update');
    Route::get('/mascotas/{id}/edit',  [MascotaController::class, 'edit'])->name('mascotas.edit');  
    Route::delete('/mascotas/{id}', [MascotaController::class, 'destroy'])->name('mascotas.destroy');
    Route::get('/mascotas/{id}/detalle', [MascotaController::class, 'detalle'])->name('mascotas.detalle');
    Route::get('/mascotas/{mascota}/historias', [HistoriaClinicaController::class, 'porMascota'])->name('mascotas.historias.index');
    Route::get('/mascotas/historia/{historia}', [HistoriaClinicaController::class, 'detalle'])->name('mascotas.historia.show');
    Route::put('/mascotas/historia/{historia}', [HistoriaClinicaController::class, 'actualizarDesdeMascota'])->name('mascotas.historia.update');

    /*
    |--------------------------------------------------------------------------
    | Historias Clínicas - CRUD con modales
    |--------------------------------------------------------------------------
    */
    Route::get('/historias', [HistoriaClinicaController::class, 'index'])->name('historias');
    Route::get('/historias/datatable', [HistoriaClinicaController::class, 'datatable'])->name('historias.datatable');
    Route::get('/historias/crear', [HistoriaClinicaController::class, 'create'])->name('historias.crear');
    Route::post('/historias', [HistoriaClinicaController::class, 'store'])->name('historias.store');
    Route::get('/historias/{id}', [HistoriaClinicaController::class, 'show'])->name('historias.show');
    Route::get('/historias/{id}/detalle', [HistoriaClinicaController::class, 'detalle'])->name('historias.detalle');
    Route::get('/historias/{id}/editar', [HistoriaClinicaController::class, 'edit'])->name('historias.editar');
    Route::put('/historias/{id}', [HistoriaClinicaController::class, 'update'])->name('historias.update');
    Route::delete('/historias/{id}', [HistoriaClinicaController::class, 'destroy'])->name('historias.destroy');
    Route::post('/historias/{id}/adjunto', [HistoriaClinicaController::class, 'uploadAttachment'])->name('historias.adjunto');

 /*
    |--------------------------------------------------------------------------
    | EXAMENES
    |--------------------------------------------------------------------------
    */

    Route::post('/mascotas/{mascota}/examenes', [ExamenController::class, 'store'])->name('examenes.store');
    Route::delete('/examenes/{examen}', [ExamenController::class, 'destroy'])->name('examenes.destroy');
    

  /*
    |--------------------------------------------------------------------------
    | VACUNACIÓN
    |--------------------------------------------------------------------------
    */
    Route::get('/vacunas', [VacunacionController::class, 'index'])->name('vacunacion');
    Route::get('/vacunas/datatable', [VacunacionController::class, 'datatable'])->name('vacunas.datatable');
    Route::get('/vacunas/crear', [VacunacionController::class, 'create'])->name('vacunas.crear');
    Route::post('/vacunas', [VacunacionController::class, 'store'])->name('vacunas.store');
    Route::get('/vacunas/{id}/detalle', [VacunacionController::class, 'detalle'])->name('vacunas.detalle');
    Route::get('/vacunas/{id}/editar', [VacunacionController::class, 'edit'])->name('vacunas.editar');
    Route::put('/vacunas/{id}', [VacunacionController::class, 'update'])->name('vacunas.update');
    Route::delete('/vacunas/{id}', [VacunacionController::class, 'destroy'])->name('vacunas.destroy');
    Route::get('/vacunas/mascota/{mascotaId}', [VacunacionController::class, 'getByMascota'])->name('vacunas.mascota');

    /*
    |--------------------------------------------------------------------------
    | DESPARASITACIÓN
    |--------------------------------------------------------------------------
    */
Route::post('/mascotas/{mascota}/aplicaciones/vacuna', [AplicacionMascotaController::class, 'storeVacuna'])->name('aplicaciones.vacuna.store');
Route::post('/mascotas/{mascota}/aplicaciones/desparasitacion', [AplicacionMascotaController::class, 'storeDesparasitacion'])->name('aplicaciones.desparasitacion.store');
Route::delete('/aplicaciones/vacuna/{aplicacion}', [AplicacionMascotaController::class, 'destroyVacuna'])->name('aplicaciones.vacuna.destroy');
Route::delete('/aplicaciones/desparasitacion/{aplicacion}', [AplicacionMascotaController::class, 'destroyDesparasitacion'])->name('aplicaciones.desparasitacion.destroy');

    /*
    |--------------------------------------------------------------------------
    | Productos - CRUD con modales
    |--------------------------------------------------------------------------
    */

    /*
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos');
    Route::get('/productos/datatable', [ProductoController::class, 'datatable'])->name('productos.datatable');
    Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/productos/{id}/stock', [ProductoController::class, 'ajustarStock'])->name('productos.stock');
    Route::get('/productos/{id}/detalle', [ProductoController::class, 'detalle'])->name('productos.detalle');
  */

   
    /*
    |--------------------------------------------------------------------------
    | Compras (a proveedores) — recepción actualiza el stock de Inventario
    |--------------------------------------------------------------------------
    */
    Route::get('/compras', [CompraController::class, 'index'])->name('compras');
    Route::get('/compras/datatable', [CompraController::class, 'datatable'])->name('compras.datatable');
    Route::get('/compras/{id}', [CompraController::class, 'show'])->name('compras.show');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::put('/compras/{id}', [CompraController::class, 'update'])->name('compras.update');
    Route::delete('/compras/{id}', [CompraController::class, 'destroy'])->name('compras.destroy');
    Route::post('/compras/{id}/recibir', [CompraController::class, 'recibir'])->name('compras.recibir');
    Route::get('/compras/{id}/detalle', [CompraController::class, 'detalle'])->name('compras.detalle');
    Route::post('/compras/proveedores', [CompraController::class, 'storeProveedor'])->name('compras.proveedores.store');

        /*
    |--------------------------------------------------------------------------
    | Facturación
    |--------------------------------------------------------------------------
    */
    Route::get('/facturas', [App\Http\Controllers\FacturacionController::class, 'index'])->name('facturas');
    Route::get('/facturas/datatable', [App\Http\Controllers\FacturacionController::class, 'datatable'])->name('facturas.datatable');
    Route::get('/facturas/crear', [App\Http\Controllers\FacturacionController::class, 'create'])->name('facturas.crear');
    Route::post('/facturas', [App\Http\Controllers\FacturacionController::class, 'store'])->name('facturas.store');
    Route::get('/facturas/{id}/detalle', [App\Http\Controllers\FacturacionController::class, 'detalle'])->name('facturas.detalle');
    Route::get('/facturas/{id}/editar', [App\Http\Controllers\FacturacionController::class, 'edit'])->name('facturas.editar');
    Route::put('/facturas/{id}', [App\Http\Controllers\FacturacionController::class, 'update'])->name('facturas.update');
    Route::delete('/facturas/{id}', [App\Http\Controllers\FacturacionController::class, 'destroy'])->name('facturas.destroy');
    Route::get('/facturas/{id}/pago', [App\Http\Controllers\FacturacionController::class, 'pago'])->name('facturas.pago');
    Route::post('/facturas/{id}/pago', [App\Http\Controllers\FacturacionController::class, 'registrarPago'])->name('facturas.registrarPago');
    Route::get('/facturas/{id}/pdf', [App\Http\Controllers\FacturacionController::class, 'generarPDF'])->name('facturas.pdf');


     /*
    |--------------------------------------------------------------------------
    | Inventario (Producto / CategoriaProducto)
    |--------------------------------------------------------------------------
    */
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::get('/inventario/datatable', [InventarioController::class, 'datatable'])->name('inventario.datatable');
    Route::get('/inventario/{id}', [InventarioController::class, 'show'])->name('inventario.show');
    Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
    Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
    Route::post('/inventario/{id}/stock', [InventarioController::class, 'ajustarStock'])->name('inventario.stock');

    /*
    |--------------------------------------------------------------------------
    | Ventas - CRUD con modales
    |--------------------------------------------------------------------------
    */
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas');
    Route::get('/ventas/datatable', [VentaController::class, 'datatable'])->name('ventas.datatable');
    Route::get('/ventas/pos', [VentaController::class, 'pos'])->name('ventas.pos');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::delete('/ventas/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy');
    Route::get('/ventas/{id}/pdf', [VentaController::class, 'generarPDF'])->name('ventas.pdf');
    Route::get('/ventas/{id}/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
    Route::get('/ventas/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('ventas.buscar-clientes');
    Route::get('/ventas/buscar-productos', [VentaController::class, 'buscarProductos'])->name('ventas.buscar-productos');
    Route::get('/ventas/buscar-mascotas', [VentaController::class, 'buscarMascotas'])->name('ventas.buscar-mascotas');
    Route::get('/ventas/{venta}/imprimir', [VentaController::class, 'datosImpresion'])->name('ventas.imprimir');
   


   /*
    |--------------------------------------------------------------------------
    | CAJA
    |--------------------------------------------------------------------------
    */
    Route::get('/caja', [CajaController::class, 'index'])->name('caja');
    Route::get('/caja/datatable', [CajaController::class, 'datatable'])->name('caja.datatable');
    Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');
    Route::post('/caja/ingreso', [CajaController::class, 'ingreso'])->name('caja.ingreso');
    Route::post('/caja/egreso', [CajaController::class, 'egreso'])->name('caja.egreso');
    Route::get('/caja/resumen', [CajaController::class, 'resumen'])->name('caja.resumen');
    Route::get('/caja/movimiento/{id}', [CajaController::class, 'movimiento'])->name('caja.movimiento');


    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/citas', [ReporteController::class, 'citas'])->name('reportes.citas');
    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('/reportes/clientes', [ReporteController::class, 'clientes'])->name('reportes.clientes');
    Route::get('/reportes/exportar-pdf', [ReporteController::class, 'exportarPDF'])->name('reportes.exportar');

   /*
    |--------------------------------------------------------------------------
    | Perfil de Usuario
    |--------------------------------------------------------------------------
    */
    // 🔥 RUTA 'perfil' - Debe coincidir con la que usa el modelo User
    Route::get('/perfil/{id?}', [PerfilController::class, 'show'])->name('perfil');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.editar');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.actualizar');
    Route::delete('/perfil', [PerfilController::class, 'destroy'])->name('perfil.eliminar');

    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/datatable', [UsuarioController::class, 'datatable'])->name('usuarios.datatable');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.actualizar');
});