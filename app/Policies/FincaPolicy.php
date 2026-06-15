<?php
declare(strict_types=1);
namespace App\Policies;

use App\Models\Finca;
use App\Models\Usuario;

class FincaPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Finca $finca): bool
    {
        if ($usuario->esAdministrador()) {
            return true;
        }

        if ($usuario->esGanadero()) {
            return $finca->propietario_id === $usuario->id;
        }

        if ($usuario->esVeterinario()) {
            return $finca->asignaciones()
                ->where('veterinario_id', $usuario->id)
                ->where('esta_activa', true)
                ->exists();
        }

        return false;
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->esGanadero();
    }

    public function update(Usuario $usuario, Finca $finca): bool
    {
        return $usuario->esGanadero() && $finca->propietario_id === $usuario->id;
    }

    public function delete(Usuario $usuario, Finca $finca): bool
    {
        return $usuario->esGanadero() && $finca->propietario_id === $usuario->id;
    }
}