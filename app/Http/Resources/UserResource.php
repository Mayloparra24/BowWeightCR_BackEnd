<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_completo' => $this->nombre_completo,
            'correo_electronico' => $this->correo_electronico,
            'rol' => $this->rol,
            'esta_activo' => $this->esta_activo,
            'debe_cambiar_contrasena' => $this->debe_cambiar_contrasena,
            'correo_verificado_en' => $this->correo_verificado_en?->toIso8601String(),
            'creado_en' => $this->created_at?->toIso8601String(),
        ];
    }
}
