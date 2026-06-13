<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fotografia>
 */
class FotografiaFactory extends Factory
{
    protected $model = Fotografia::class;

    public function definition(): array
    {
        return [
            'bovino_id' => Bovino::factory(),
            'capturada_por' => Usuario::factory(),
            'ruta_archivo' => 'fotografias/' . now()->format('Y/m') . '/' . fake()->uuid() . '.jpg',
            'estado_procesamiento' => fake()->randomElement(['pendiente', 'completado', 'fallido']),
            'capturada_el' => now(),
            'sincronizada_el' => fake()->optional()->dateTime(),
        ];
    }
}
