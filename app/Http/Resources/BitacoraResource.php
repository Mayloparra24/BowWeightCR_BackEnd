<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BitacoraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario' => new UserResource($this->whenLoaded('usuario')),
            'accion' => $this->accion,
            'entidad_tipo' => $this->entidad_tipo,
            'entidad_id' => $this->entidad_id,
            'descripcion' => $this->descripcion,
            'direccion_ip' => $this->direccion_ip,
            'creada_el' => $this->creada_el?->toIso8601String(),
        ];
    }
}
