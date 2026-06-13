<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Raza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Raza>
 */
class RazaFactory extends Factory
{
    protected $model = Raza::class;

    public function definition(): array
    {
        return [
            'nombre_raza' => fake()->unique()->word(),
            'enfoque' => fake()->randomElement(['carne', 'doble_proposito', 'leche']),
            'constante_peso' => fake()->randomFloat(2, 200, 280),
            'descripcion' => fake()->sentence(),
        ];
    }
}
