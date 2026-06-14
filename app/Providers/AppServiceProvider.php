<?php
namespace App\Providers;

use App\Models\AsignacionVeterinario;
use App\Models\Bovino;
use App\Models\Finca;
use App\Policies\AsignacionVeterinarioPolicy;
use App\Policies\BovinoPolicy;
use App\Policies\FincaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Usuario;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Finca::class, FincaPolicy::class);
        Gate::policy(Bovino::class, BovinoPolicy::class);
        Gate::policy(AsignacionVeterinario::class, AsignacionVeterinarioPolicy::class);

        Gate::define('administrar-usuarios', function (Usuario $usuario) {
            return $usuario->esAdministrador();
        });
    }
}