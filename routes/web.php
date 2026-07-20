<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Grupo de rutas protegidas (requieren login)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('productos', ProductController::class);
    Route::get('/productos/exportar', [ProductController::class, 'exportCsv'])->name('productos.export');
    
    Route::resource('categorias', CategoryController::class);
    
});
// Redirigir al dashboard después del login
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');
// Rutas de autenticación (generadas por Breeze)
require __DIR__.'/auth.php';