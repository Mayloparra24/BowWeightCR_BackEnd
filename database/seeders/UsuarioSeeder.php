<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nombre_completo'    => 'Iván Chavarría',
            'correo_electronico' => 'ganadero@bovweight.com',
            'contrasena_hash'    => bcrypt('password123'),
            'rol'                => 'ganadero',
            'esta_activo'        => true,
        ]);

        Usuario::create([
            'nombre_completo'    => 'Dr. Roberto Solano',
            'correo_electronico' => 'veterinario@bovweight.com',
            'contrasena_hash'    => bcrypt('password123'),
            'rol'                => 'veterinario',
            'esta_activo'        => true,
        ]);

        Usuario::create([
            'nombre_completo'    => 'Administrador Sistema',
            'correo_electronico' => 'admin@bovweight.com',
            'contrasena_hash'    => bcrypt('password123'),
            'rol'                => 'administrador',
            'esta_activo'        => true,
        ]);

        Usuario::create([
            'nombre_completo'    => 'Asistente de Campo',
            'correo_electronico' => 'asistente@bovweight.cr',
            'contrasena_hash'    => bcrypt('BovWeight2026!'),
            'rol'                => 'asistente',
            'esta_activo'        => true,
        ]);
    }
}