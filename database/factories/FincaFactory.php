<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Finca;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Finca>
 */
class FincaFactory extends Factory
{
    protected $model = Finca::class;

    public function definition(): array
    {
        return [
            'propietario_id' => Usuario::factory(),
            'nombre_finca' => fake()->company().' - Finca',
            'ubicacion' => fake()->streetAddress(),
            'canton' => fake()->randomElement(['Liberia', 'Bagaces', 'Cañas', 'Santa Cruz', 'Nicoya']),
            'provincia' => 'Guanacaste',
            'esta_activa' => true,
        ];
    }
}
