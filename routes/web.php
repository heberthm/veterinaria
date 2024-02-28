<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();

Route::get('inicio', [App\Http\Controllers\HomeController::class, 'index'])->name('inicio');

Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'registration'])->name('register');

Route::get('clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('clientes');



