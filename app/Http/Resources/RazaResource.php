<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RazaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_raza' => $this->nombre_raza,
            'enfoque' => $this->enfoque,
            'constante_peso' => (float) $this->constante_peso,
            'descripcion' => $this->descripcion,
        ];
    }
}
