<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Bovino;
use App\Models\RegistroPesaje;
use App\Models\Usuario;

class PesajePolicy
{
    public function viewAny(Usuario $usuario, Bovino $bovino): bool
    {
        return $this->puedeAccederBovino($usuario, $bovino);
    }

    public function create(Usuario $usuario, Bovino $bovino): bool
    {
        return $this->puedeAccederBovino($usuario, $bovino);
    }

    public function update(Usuario $usuario, RegistroPesaje $pesaje): bool
    {
        return $this->puedeAccederBovino($usuario, $pesaje->bovino);
    }

    private function puedeAccederBovino(Usuario $usuario, Bovino $bovino): bool
    {
        if ($usuario->esAdministrador()) {
            return true;
        }

        if ($usuario->esGanadero()) {
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
}
