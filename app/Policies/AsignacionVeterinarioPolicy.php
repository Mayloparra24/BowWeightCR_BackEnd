<?php
declare(strict_types=1);
namespace App\Policies;

use App\Models\AsignacionVeterinario;
use App\Models\Usuario;

class AsignacionVeterinarioPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->esGanadero() || $usuario->esAdministrador();
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->esGanadero();
    }

    public function delete(Usuario $usuario, AsignacionVeterinario $asignacion): bool
    {
        return $usuario->esGanadero() && $asignacion->asignado_por === $usuario->id;
    }
}