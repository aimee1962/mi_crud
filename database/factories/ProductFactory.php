<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category; // <-- Necesario para obtener las categorías
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'description' => $this->faker->sentence(15),
            'image' => null, // Las imágenes las dejamos nulas para evitar errores de librería GD (las subes tú manualmente si quieres)
            
            // 🔥 ASIGNACIÓN SEGURA DE CATEGORÍA:
            // Toma una categoría aleatoria de las que acabamos de crear en el Seeder.
            // Como el Seeder las crea PRIMERO, esto SIEMPRE encontrará una.
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}