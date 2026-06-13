<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_completo' => fake()->name(),
            'correo_electronico' => fake()->unique()->safeEmail(),
            'correo_verificado_en' => now(),
            'contrasena_hash' => static::$password ??= Hash::make('password'),
            'rol' => 'ganadero',
            'esta_activo' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function ganadero(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'ganadero',
        ]);
    }

    public function veterinario(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'veterinario',
        ]);
    }

    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'esta_activo' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'correo_verificado_en' => null,
        ]);
    }
}
