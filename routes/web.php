<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController; 

Route::get('/', function () {
    return view('welcome');
});

// Con esto tenemos TODAS las rutas CRUD:
// GET /productos (index)
// GET /productos/create (create)
// POST /productos (store)
// GET /productos/{id}/edit (edit)
// PUT/PATCH /productos/{id} (update)
// DELETE /productos/{id} (destroy)
Route::resource('productos', ProductController::class);
Route::resource('categorias', CategoryController::class);