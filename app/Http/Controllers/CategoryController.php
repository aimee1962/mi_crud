<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lista de categorías con paginación y búsqueda.
     */
    public function index()
    {
        $search = request('search');

        $categories = Category::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate(5)
        ->appends(['search' => $search]);

        return view('categories.index', compact('categories'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Guarda una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categorias.index')
                         ->with('success', '¡Categoría creada exitosamente!');
    }

    /**
     * Muestra el formulario para editar una categoría.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Actualiza una categoría en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categorias.index')
                         ->with('success', '¡Categoría actualizada exitosamente!');
    }

    /**
     * Elimina una categoría (si no tiene productos asociados).
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Verificar si la categoría tiene productos
        if ($category->products()->count() > 0) {
            return redirect()->route('categorias.index')
                             ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return redirect()->route('categorias.index')
                         ->with('success', '¡Categoría eliminada exitosamente!');
    }
}