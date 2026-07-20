<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;  // <--- IMPORTA EL MODELO AQUÍ (FUERA DE LA CLASE)
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas para las tarjetas
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $mostExpensive = Product::orderBy('price', 'desc')->first();
        $cheapest = Product::orderBy('price', 'asc')->first();
        $recentProducts = Product::with('category')->latest()->take(5)->get();

        // Datos para el gráfico (por categoría)
        $categories = Category::withCount('products')->withSum('products', 'stock')->get();
        $chartLabels = $categories->pluck('name');
        $chartCounts = $categories->pluck('products_count');
        $chartStocks = $categories->pluck('products_sum_stock');

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'mostExpensive',
            'cheapest',
            'recentProducts',
            'chartLabels',
            'chartCounts',
            'chartStocks'
        ));
    }
}