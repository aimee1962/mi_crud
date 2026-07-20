<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController; // <--- IMPORTA ESTE CONTROLADOR

Route::get('/', function () {
    return redirect()->route('dashboard'); // Redirige a /dashboard
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/productos/exportar', [ProductController::class, 'exportCsv'])->name('productos.export');
Route::resource('productos', ProductController::class);
Route::resource('categorias', CategoryController::class);