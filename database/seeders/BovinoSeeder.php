<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\Bovino;
use Illuminate\Database\Seeder;

class BovinoSeeder extends Seeder
{
    public function run(): void
    {
        Bovino::create([
    'finca_id'         => 1,
    'raza_id'          => 1,
    'numero_arete'     => 'CR-010-2024',
    'nombre_animal'    => 'Bella',
    'sexo'             => 'hembra',
    'fecha_nacimiento' => '2021-05-10',
    'estado'           => 'activo',
]);

Bovino::create([
    'finca_id'         => 1,
    'raza_id'          => 2,
    'numero_arete'     => 'CR-011-2024',
    'nombre_animal'    => 'Toro Negro',
    'sexo'             => 'macho',
    'fecha_nacimiento' => '2020-03-15',
    'estado'           => 'activo',
]);

Bovino::create([
    'finca_id'         => 2,
    'raza_id'          => 3,
    'numero_arete'     => 'CR-012-2024',
    'nombre_animal'    => 'Lechera',
    'sexo'             => 'hembra',
    'fecha_nacimiento' => '2022-01-20',
    'estado'           => 'activo',
]);
    }
}