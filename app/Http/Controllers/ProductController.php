<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos con orden y búsqueda.
     */
    public function index()
    {
        $search = request('search');
        $sort = request('sort', 'id');
        $direction = request('direction', 'asc');

        // Columnas permitidas para ordenar (seguridad)
        $allowedSorts = ['id', 'name', 'price', 'stock', 'category_name'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $query = Product::with('category');

        // Búsqueda por nombre
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Orden especial por nombre de categoría (JOIN)
        if ($sort === 'category_name') {
            $query->join('categories', 'products.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $direction)
                ->select('products.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $products = $query->paginate(5)->appends([
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction
        ]);

        return view('products.index', compact('products'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Guarda un nuevo producto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image' => $imagePath,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualiza un producto en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'description', 'category_id']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto de la base de datos.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}