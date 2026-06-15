<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesajeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bovino_id' => $this->bovino_id,
            'fotografia_id' => $this->fotografia_id,
            'creado_por' => new UserResource($this->whenLoaded('creadoPor')),
            'peso_registrado' => $this->peso_registrado !== null ? (float) $this->peso_registrado : null,
            'peso_estimado' => $this->peso_estimado !== null ? (float) $this->peso_estimado : null,
            'peso_final' => $this->pesoFinal(),
            'tipo_pesaje' => $this->tipo_pesaje,
            'es_correccion_manual' => (bool) $this->es_correccion_manual,
            'notas_correccion' => $this->notas_correccion,
            'confianza_ia' => $this->confianza_ia !== null ? (float) $this->confianza_ia : null,
            'registrado_el' => $this->registrado_el?->toIso8601String(),
            'bovino' => new BovinoResource($this->whenLoaded('bovino')),
            'fotografia' => $this->whenLoaded('fotografia'),
        ];
    }
}
