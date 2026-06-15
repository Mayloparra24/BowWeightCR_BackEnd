<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Contracts\IEstimadorPeso;
use App\Contracts\IEstimationStrategy;
use App\DTOs\WeightEstimationRequest;
use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\RegistroPesaje;
use App\Services\ImageStorageService;

class OnlineEstimationStrategy implements IEstimationStrategy
{
    public function __construct(
        private readonly IEstimadorPeso $estimator,
        private readonly ImageStorageService $storage,
    ) {}

    public function estimate(Bovino $bovino, WeightEstimationRequest $request, int $capturedBy): RegistroPesaje
    {
        $path = $this->storage->upload($request->image, 'fotografias');

        $fotografia = Fotografia::create([
            'bovino_id' => $bovino->id,
            'capturada_por' => $capturedBy,
            'ruta_archivo' => $path,
            'estado_procesamiento' => 'procesando',
            'capturada_el' => now(),
        ]);

        $result = $this->estimator->estimar($request);

        $fotografia->marcarComoCompletado();

        return RegistroPesaje::create([
            'bovino_id' => $bovino->id,
            'fotografia_id' => $fotografia->id,
            'creado_por' => $capturedBy,
            'peso_estimado' => $result->estimatedWeight,
            'peso_registrado' => $result->estimatedWeight,
            'tipo_pesaje' => 'ia',
            'confianza_ia' => $result->confidence,
            'registrado_el' => now(),
        ]);
    }
}
