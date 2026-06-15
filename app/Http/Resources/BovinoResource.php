<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BovinoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'finca_id' => $this->finca_id,
            'raza_id' => $this->raza_id,
            'numero_arete' => $this->numero_arete,
            'nombre_animal' => $this->nombre_animal,
            'sexo' => $this->sexo,
            'fecha_nacimiento' => $this->fecha_nacimiento?->toDateString(),
            'estado' => $this->estado,
            'motivo_inactividad' => $this->motivo_inactividad,
            'fecha_inactividad' => $this->fecha_inactividad?->toDateString(),
            'notas' => $this->notas,
            'creado_en' => $this->created_at?->toIso8601String(),
            'finca' => new FincaResource($this->whenLoaded('finca')),
            'raza' => new RazaResource($this->whenLoaded('raza')),
            'pesajes' => PesajeResource::collection($this->whenLoaded('pesajes')),
        ];
    }
}
