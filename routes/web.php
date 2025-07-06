<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPeliculasController;
use App\Http\Controllers\ClienteReservasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/peliculas', [AdminPeliculasController::class, 'index'])->name('admin.peliculas');
    Route::get('/peliculas/form/{id?}', [AdminPeliculasController::class, 'form'])->name('admin.peliculas.form');
    Route::post('/peliculas/delete/{id}', [AdminPeliculasController::class, 'destroy'])->name('admin.peliculas.delete');
    Route::post('/peliculas/store', [AdminPeliculasController::class, 'store'])->name('admin.peliculas.store');
    Route::post('/peliculas/update/{id}', [AdminPeliculasController::class, 'update'])->name('admin.peliculas.update');
    
    // Rutas para reportes
    Route::get('/reportes/funciones', [AdminPeliculasController::class, 'exportarReporteFunciones'])->name('admin.reportes.funciones');
    Route::get('/reportes/asientos', [AdminPeliculasController::class, 'exportarReporteAsientos'])->name('admin.reportes.asientos');
    Route::get('/reportes/peliculas-por-cine', [AdminPeliculasController::class, 'exportarReportePeliculasPorCine'])->name('admin.reportes.peliculas-por-cine');
    Route::get('/reportes/peliculas-por-genero', [AdminPeliculasController::class, 'exportarReportePeliculasPorGenero'])->name('admin.reportes.peliculas-por-genero');
    Route::get('/reportes/generos-mas-reservados', [AdminPeliculasController::class, 'exportarReporteGenerosMasReservados'])->name('admin.reportes.generos-mas-reservados');
});

Route::prefix('cliente')->group(function () {
    Route::get('/reservar', [ClienteReservasController::class, 'form'])->name('cliente.reservar.form');
    Route::post('/reservar', [ClienteReservasController::class, 'store'])->name('cliente.reservar.store');
    Route::get('/ticket/{id}', [ClienteReservasController::class, 'ticket'])->name('cliente.reservar.ticket');
});
