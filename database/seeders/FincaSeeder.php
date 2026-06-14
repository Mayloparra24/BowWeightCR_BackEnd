<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\Finca;
use Illuminate\Database\Seeder;

class FincaSeeder extends Seeder
{
    public function run(): void
    {
        Finca::create([
            'propietario_id' => 1,
            'nombre_finca'   => 'Finca La Esperanza',
            'ubicacion'      => 'Liberia, Guanacaste',
            'canton'         => 'Liberia',
            'provincia'      => 'Guanacaste',
            'esta_activa'    => true,
        ]);

        Finca::create([
            'propietario_id' => 1,
            'nombre_finca'   => 'Finca Las Palmas',
            'ubicacion'      => 'Bagaces, Guanacaste',
            'canton'         => 'Bagaces',
            'provincia'      => 'Guanacaste',
            'esta_activa'    => true,
        ]);
    }
}