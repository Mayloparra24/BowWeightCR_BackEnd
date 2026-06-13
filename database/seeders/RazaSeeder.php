<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Raza;
use Illuminate\Database\Seeder;

class RazaSeeder extends Seeder
{
    public function run(): void
    {
        $razas = [
            ['nombre_raza' => 'Brahman', 'enfoque' => 'carne', 'constante_peso' => 250.00, 'descripcion' => 'Raza cebuina adaptada al trópico, muy común en Guanacaste'],
            ['nombre_raza' => 'Pardo Suizo', 'enfoque' => 'doble_proposito', 'constante_peso' => 260.00, 'descripcion' => 'Raza lechera y de carne versátil'],
            ['nombre_raza' => 'Holstein', 'enfoque' => 'leche', 'constante_peso' => 240.00, 'descripcion' => 'Raza lechera de alta producción'],
            ['nombre_raza' => 'Jersey', 'enfoque' => 'leche', 'constante_peso' => 220.00, 'descripcion' => 'Raza lechera de tamaño mediano'],
            ['nombre_raza' => 'Criollo', 'enfoque' => 'carne', 'constante_peso' => 230.00, 'descripcion' => 'Ganado local adaptado a condiciones costarricenses'],
            ['nombre_raza' => 'Gyr', 'enfoque' => 'leche', 'constante_peso' => 235.00, 'descripcion' => 'Raza cebuina lechera'],
            ['nombre_raza' => 'Canchim', 'enfoque' => 'carne', 'constante_peso' => 255.00, 'descripcion' => 'Cruce de Charolais con cebuinos'],
        ];

        foreach ($razas as $raza) {
            Raza::firstOrCreate(
                ['nombre_raza' => $raza['nombre_raza']],
                $raza
            );
        }
    }
}
