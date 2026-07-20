<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de productos.
     */
    public function run(): void
    {
        // Opción 1: Crear 1 producto fijo (para tener un referente)
        Product::create([
            'name' => 'Producto de Prueba',
            'price' => 99.99,
            'description' => 'Este es un producto de ejemplo para pruebas.',
            'image' => null, // Sin imagen
        ]);

        // Opción 2: Generar 50 productos falsos usando la Factory
        // Puedes cambiar el número (50) por el que quieras
        Product::factory(50)->create();
    }
}