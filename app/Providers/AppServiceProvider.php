<?php
namespace App\Providers;

use App\Models\AsignacionVeterinario;
use App\Models\Bovino;
use App\Models\Finca;
use App\Models\RegistroPesaje;
use App\Models\Usuario;
use App\Policies\AsignacionVeterinarioPolicy;
use App\Policies\BovinoPolicy;
use App\Policies\FincaPolicy;
use App\Policies\PesajePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Finca::class, FincaPolicy::class);
        Gate::policy(Bovino::class, BovinoPolicy::class);
        Gate::policy(AsignacionVeterinario::class, AsignacionVeterinarioPolicy::class);
        Gate::policy(RegistroPesaje::class, PesajePolicy::class);

        Gate::define('administrar-usuarios', function (Usuario $usuario) {
            return $usuario->esAdministrador();
        });

        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('estimacion-ia', function ($request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}