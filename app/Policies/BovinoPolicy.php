<?php
declare(strict_types=1);
namespace App\Policies;

use App\Models\Bovino;
use App\Models\Usuario;

class BovinoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Bovino $bovino): bool
    {
        if ($usuario->esAdministrador()) {
            return true;
        }

        if ($usuario->esGanadero() || $usuario->esAsistente()) {
            return $bovino->finca->propietario_id === $usuario->id;
        }

        if ($usuario->esVeterinario()) {
            return $bovino->finca->asignaciones()
                ->where('veterinario_id', $usuario->id)
                ->where('esta_activa', true)
                ->exists();
        }

        return false;
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->esGanadero() || $usuario->esAsistente();
    }

    public function update(Usuario $usuario, Bovino $bovino): bool
    {
        return ($usuario->esGanadero() || $usuario->esAsistente()) &&
            $bovino->finca->propietario_id === $usuario->id;
    }

    public function delete(Usuario $usuario, Bovino $bovino): bool
    {
        return ($usuario->esGanadero() || $usuario->esAsistente()) &&
            $bovino->finca->propietario_id === $usuario->id;
    }
}