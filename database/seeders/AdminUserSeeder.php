<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nombre_completo' => 'Administrador BovWeightCR',
            'correo_electronico' => 'admin@bovweightcr.com',
            'contrasena_hash' => 'BovWeight2026!',
            'rol' => 'administrador',
            'esta_activo' => true,
        ]);

        $this->command->warn('Usuario admin creado: admin@bovweightcr.com / BovWeight2026!');
        $this->command->warn('⚠️  CAMBIAR LA CONTRASEÑA DESPUÉS DEL PRIMER INICIO DE SESIÓN.');
    }
}
