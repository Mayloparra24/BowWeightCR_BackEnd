<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\RegistroPesaje;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistroPesaje>
 */
class RegistroPesajeFactory extends Factory
{
    protected $model = RegistroPesaje::class;

    public function definition(): array
    {
        $tipo = fake()->randomElement(['ia', 'manual']);
        $pesoEstimado = $tipo === 'ia' ? fake()->randomFloat(2, 200, 600) : null;
        $pesoRegistrado = fake()->randomFloat(2, 200, 600);
        $correccion = fake()->boolean(20);

        return [
            'bovino_id' => Bovino::factory(),
            'fotografia_id' => $tipo === 'ia' ? Fotografia::factory() : null,
            'creado_por' => Usuario::factory(),
            'peso_estimado' => $pesoEstimado,
            'peso_registrado' => $correccion || $tipo === 'manual' ? $pesoRegistrado : null,
            'es_correccion_manual' => $correccion,
            'tipo_pesaje' => $tipo,
            'notas_correccion' => $correccion ? fake()->sentence() : null,
            'confianza_ia' => $tipo === 'ia' ? fake()->randomFloat(4, 0.7, 0.99) : null,
            'registrado_el' => now(),
        ];
    }
}
