<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FincaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'propietario_id' => $this->propietario_id,
            'nombre_finca' => $this->nombre_finca,
            'ubicacion' => $this->ubicacion,
            'canton' => $this->canton,
            'provincia' => $this->provincia,
            'esta_activa' => $this->esta_activa,
            'creado_en' => $this->created_at?->toIso8601String(),
        ];
    }
}
