<?php
declare(strict_types=1);
namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RazaSeeder::class,
            UsuarioSeeder::class,
            FincaSeeder::class,
            BovinoSeeder::class,
        ]);
    }
}