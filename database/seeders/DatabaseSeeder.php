<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear las 5 categorías principales
        $categories = ['Electrónica', 'Ropa', 'Libros', 'Hogar', 'Deportes'];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }

        // 2. Crear 50 productos sin usar Factory (para evitar errores de imágenes)
        for ($i = 1; $i <= 50; $i++) {
            Product::create([
                'name' => 'Producto de prueba ' . $i,
                'price' => rand(10, 500) + 0.99, // Precio aleatorio entre 10 y 500
                'description' => 'Descripción automática para el producto número ' . $i,
                'category_id' => rand(1, 5), // Asigna una categoría aleatoria (1 a 5)
                'image' => null, // Sin imagen para evitar errores
            ]);
        }

        // Mensaje de confirmación en la terminal
        $this->command->info('¡Base de datos llenada con 5 categorías y 50 productos!');
    }
}