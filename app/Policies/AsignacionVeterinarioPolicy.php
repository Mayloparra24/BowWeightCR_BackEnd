<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AsignacionVeterinario;
use App\Models\Finca;
use App\Models\Usuario;

class AsignacionVeterinarioPolicy
{
    public function viewAny(Usuario $usuario, Finca $finca): bool
    {
        if ($usuario->esAdministrador()) {
            return true;
        }

        return $finca->propietario_id === $usuario->id;
    }

    public function create(Usuario $usuario, Finca $finca): bool
    {
        if (! $usuario->esGanadero()) {
            return false;
        }

        return $finca->propietario_id === $usuario->id;
    }

    public function delete(Usuario $usuario, AsignacionVeterinario $asignacion): bool
    {
        if (! $usuario->esGanadero()) {
            return false;
        }

        return $asignacion->finca->propietario_id === $usuario->id;
    }
}
