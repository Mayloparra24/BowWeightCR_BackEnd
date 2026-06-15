<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bovino;
use App\Models\Finca;
use App\Models\Raza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bovino>
 */
class BovinoFactory extends Factory
{
    protected $model = Bovino::class;

    public function definition(): array
    {
        return [
            'finca_id' => Finca::factory(),
            'raza_id' => Raza::factory(),
            'numero_arete' => fake()->unique()->numerify('##########'),
            'nombre_animal' => fake()->optional()->firstName(),
            'sexo' => fake()->randomElement(['macho', 'hembra']),
            'fecha_nacimiento' => fake()->optional()->date(),
            'estado' => 'activo',
            'motivo_inactividad' => null,
            'fecha_inactividad' => null,
            'notas' => fake()->optional()->sentence(),
        ];
    }

    public function inactivo(string $motivo = 'vendido'): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
            'motivo_inactividad' => $motivo,
            'fecha_inactividad' => now()->subDays(fake()->numberBetween(1, 365))->toDateString(),
        ]);
    }
}
