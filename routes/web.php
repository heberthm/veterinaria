<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\AplicacionMascotaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VeterinarioController;
use App\Http\Controllers\VacunacionController;
use App\Http\Controllers\DesparasitacionAplicacionController;
use App\Http\Controllers\RoleController;
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
  Route::put('/agenda/citas/{id}', [AgendaController::class, 'update'])->name('agenda.citas.update');

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
    Route::get('/citas/eventos', [CitaController::class, 'getEvents'])->name('citas.eventos');
    Route::get('/citas/{id}', [CitaController::class, 'show'])->name('citas.show');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::put('/citas/{id}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('/citas/{id}', [CitaController::class, 'destroy'])->name('citas.destroy');
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
    Route::get('/mascotas/buscar', [MascotaController::class, 'buscar'])->name('mascotas.buscar');
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
    Route::post('historias/{mascota?}', [HistoriaClinicaController::class, 'store'])->name('historias.store');
    Route::get('historias/{mascota}', [HistoriaClinicaController::class, 'show'])->name('historias.show');
    Route::get('/historias/{id}/detalle', [HistoriaClinicaController::class, 'detalle'])->name('historias.detalle');
    Route::get('/historias/{id}/editar', [HistoriaClinicaController::class, 'edit'])->name('historias.editar');
    Route::put('historias/{historia}', [HistoriaClinicaController::class, 'update'])->name('historias.update');
    Route::delete('/historias/{id}', [HistoriaClinicaController::class, 'destroy'])->name('historias.destroy');
    Route::post('/historias/{id}/adjunto', [HistoriaClinicaController::class, 'uploadAttachment'])->name('historias.adjunto');
    Route::put('/historias/consulta/{historia}', [HistoriaClinicaController::class, 'update'])->name('historias.consulta.update');
    Route::get('historias/{historia}/pdf', [HistoriaClinicaController::class, 'pdf'])->name('historias.pdf');

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
    Route::get('/vacunas/{id}/detalle', [VacunacionController::class, 'show'])->name('vacunas.show');
    Route::get('/vacunas/{id}/editar', [VacunacionController::class, 'edit'])->name('vacunas.editar');
    Route::put('/vacunas/{id}', [VacunacionController::class, 'update'])->name('vacunas.update');
    Route::delete('/vacunas/{id}', [VacunacionController::class, 'destroy'])->name('vacunas.destroy');
    Route::get('/vacunas/mascota/{mascotaId}', [VacunacionController::class, 'getByMascota'])->name('vacunas.mascota');



     /*
    |--------------------------------------------------------------------------
    | RECETA MEDICA
    |--------------------------------------------------------------------------
    */

    Route::post('recetas/{mascota}', [RecetaController::class, 'store'])->name('recetas.store');
    Route::delete('/recetas/{receta}', [RecetaController::class, 'destroy'])->name('recetas.destroy');
    Route::get('/recetas/{receta}', [RecetaController::class, 'show'])->name('recetas.show');
    Route::put('/recetas/{receta}', [RecetaController::class, 'update'])->name('recetas.update');
    Route::get('/recetas/{receta}/pdf', [RecetaController::class, 'pdf'])->name('recetas.pdf');
    Route::get('recetas/{receta}/pdf-preview', [RecetaController::class, 'pdfPreview'])->name('recetas.pdf.preview');

    /*
    |--------------------------------------------------------------------------
    | DESPARASITACIÓN
    |--------------------------------------------------------------------------
    */
    Route::get('/desparasitaciones', [DesparasitacionAplicacionController::class, 'index'])->name('desparasitaciones.index');
    Route::get('/desparasitaciones/datatable', [DesparasitacionAplicacionController::class, 'datatable'])->name('desparasitaciones.datatable');
    Route::post('/desparasitaciones', [DesparasitacionAplicacionController::class, 'store'])->name('desparasitaciones.store');
    Route::put('/desparasitaciones/{id}', [DesparasitacionAplicacionController::class, 'update'])->name('desparasitaciones.update');
    Route::get('/desparasitaciones/{id}/detalle', [DesparasitacionAplicacionController::class, 'detalle'])->name('desparasitaciones.detalle');
    Route::delete('/desparasitaciones/{id}', [DesparasitacionAplicacionController::class, 'destroy'])->name('desparasitaciones.destroy');
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
    | Veterinarios
    |--------------------------------------------------------------------------
    */

    Route::get('/veterinarios/buscar', [VeterinarioController::class, 'buscar'])->name('veterinarios.buscar');
    
   
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
    Route::get('/ventas/buscar-clientes', [VentaController::class, 'buscarClientes'])->name('ventas.buscar-clientes');
    Route::get('/ventas/buscar-productos', [VentaController::class, 'buscarProductos'])->name('ventas.buscar-productos');
    Route::get('/ventas/buscar-mascotas', [VentaController::class, 'buscarMascotas'])->name('ventas.buscar-mascotas');
    Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::delete('/ventas/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy');
    Route::get('/ventas/{id}/pdf', [VentaController::class, 'generarPDF'])->name('ventas.pdf');
    Route::get('/ventas/{id}/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
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
    // Alias para el menú actual (layouts/app.blade.php usa route('reportes'))
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');

    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/index',     [ReporteController::class, 'index'])->name('index');
        Route::get('/data',      [ReporteController::class, 'data'])->name('data');
        Route::get('/historial', [ReporteController::class, 'historial'])->name('historial');
        Route::get('/pdf',       [ReporteController::class, 'pdf'])->name('pdf');
        Route::get('/csv',       [ReporteController::class, 'csv'])->name('csv');
    });

   /*
    |--------------------------------------------------------------------------
    | Perfil de Usuario
    |--------------------------------------------------------------------------
    */
    // 🔥 RUTA 'perfil' - Debe coincidir con la que usa el modelo User
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.editar');
    Route::get('/perfil/{id?}', [PerfilController::class, 'show'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.actualizar');
    Route::delete('/perfil', [PerfilController::class, 'destroy'])->name('perfil.eliminar');

    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/lista', [UserController::class, 'index'])->name('users.index');   // antes de {user}
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    /*
    |--------------------------------------------------------------------------
    | Roles y permisos
    |--------------------------------------------------------------------------
    */
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/permisos', [RoleController::class, 'syncPermissions'])->name('roles.syncPermissions');
    Route::get('/roles/{id}/permisos-actuales', [RoleController::class, 'getPermisosActuales'])->name('roles.getPermisosActuales');

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        Route::get('/logo', [ConfiguracionController::class, 'logo'])->name('logo');
        Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
        Route::post('/general',                    [ConfiguracionController::class, 'guardarGeneral'])->name('general');
        Route::post('/facturacion',                [ConfiguracionController::class, 'guardarFacturacion'])->name('facturacion');
        Route::post('/horario',                    [ConfiguracionController::class, 'guardarHorario'])->name('horario');
        Route::get('/catalogos/{tipo}',            [ConfiguracionController::class, 'catalogoIndex'])->name('catalogos.index');
        Route::post('/catalogos/sugeridos/{tipo}', [ConfiguracionController::class, 'catalogoSugeridos'])->name('catalogos.sugeridos');
        Route::post('/catalogos',                  [ConfiguracionController::class, 'catalogoStore'])->name('catalogos.store');
        Route::put('/catalogos/{id}',              [ConfiguracionController::class, 'catalogoUpdate'])->name('catalogos.update');
        Route::delete('/catalogos/{id}',           [ConfiguracionController::class, 'catalogoDestroy'])->name('catalogos.destroy');

        Route::get('/permisos/{rol}',              [ConfiguracionController::class, 'permisos'])->name('permisos');
        Route::post('/permisos/{rol}',             [ConfiguracionController::class, 'guardarPermisos'])->name('permisos.guardar');
    });

}); // <-- cierra Route::middleware(['auth'])->group(...) abierto en la línea 67