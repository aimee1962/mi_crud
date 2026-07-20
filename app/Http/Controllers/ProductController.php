<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;          // <-- Importante para las categorías
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos con paginación, filtro y categoría.
     */
public function index()
{
    $search = request('search');
    $sort = request('sort', 'id');          // Campo por defecto: id
    $direction = request('direction', 'asc'); // Dirección por defecto: asc

    // 🔒 Lista blanca para evitar inyecciones SQL (solo permitir estos campos)
    $allowedSorts = ['id', 'name', 'price', 'stock','created_at'];
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'id';
    }

    // 🔒 Validar dirección
    $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

    $products = Product::with('category')
        ->when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
        ->orderBy($sort, $direction)
        ->paginate(8)
        ->appends(['search' => $search, 'sort' => $sort, 'direction' => $direction]);

    return view('products.index', compact('products'));
}

    /**
     * Muestra el formulario para CREAR un nuevo producto.
     * Envía la lista de categorías al formulario.
     */
    public function create()
    {
        $categories = Category::all(); // <-- Obtiene todas las categorías
        return view('products.create', compact('categories'));
    }

    /**
     * Guarda el NUEVO producto en la base de datos (con imagen y categoría).
     */
    public function store(Request $request)
    {
        // Validaciones (incluyen categoría e imagen)
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', // <-- Valida que exista en la BD
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB máx.
        ]);

        // Procesar la imagen si se subió
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Crear el producto con todos los datos
        Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'description' => $request->description,
            'category_id' => $request->category_id, // <-- Guarda la categoría
            'image'       => $imagePath,
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', '¡Producto creado exitosamente!');
    }

    /**
     * Muestra el formulario para EDITAR un producto.
     * Envía el producto a editar y la lista de categorías.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id); // Busca el producto o da error 404
        $categories = Category::all();       // Todas las categorías para el desplegable

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualiza el producto en la base de datos (con imagen y categoría).
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validaciones (idénticas a las de store)
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Preparar los datos a actualizar (sin la imagen, se maneja aparte)
        $data = $request->only(['name', 'price', 'stock','description', 'category_id']);

        // Si suben una imagen nueva, eliminar la anterior y guardar la nueva
        if ($request->hasFile('image')) {
            // Eliminar la imagen vieja si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Guardar la nueva imagen
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Actualizar el producto
        $product->update($data);

        return redirect()
            ->route('productos.index')
            ->with('success', '¡Producto actualizado correctamente!');
    }

    /**
     * Elimina el producto (y también su imagen asociada).
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Eliminar la imagen del disco si existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Eliminar el registro de la base de datos
        $product->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', '¡Producto eliminado!');
    }
    /**
 * Exporta todos los productos a un archivo CSV.
 */
public function exportCsv()
{
    // 1. Traer todos los productos con su categoría
    $products = Product::with('category')->get();

    // 2. Configurar las cabeceras para la descarga
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename=productos_' . date('Y-m-d') . '.csv',
    ];

    // 3. Crear el contenido del CSV
    $callback = function() use ($products) {
        $file = fopen('php://output', 'w');

        // Escribir la fila de encabezados (nombres de columnas)
        fputcsv($file, ['ID', 'Nombre', 'Precio', 'Descripción', 'Categoría', 'Fecha de Creación']);

        // Escribir cada producto
        foreach ($products as $product) {
            fputcsv($file, [
                $product->id,
                $product->name,
                $product->price,
                $product->stock,
                $product->description,
                $product->category->name ?? 'Sin categoría',
                $product->created_at,
            ]);
        }

        fclose($file);
    };

    // 4. Devolver la respuesta como descarga
    return response()->stream($callback, 200, $headers);
}
}