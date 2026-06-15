<?php

declare(strict_types=1);

namespace App\Strategies;

use App\Contracts\IEstimationStrategy;
use App\DTOs\WeightEstimationRequest;
use App\Jobs\ProcessWeightEstimationJob;
use App\Models\Bovino;
use App\Models\Fotografia;
use App\Services\ImageStorageService;

class OfflineEstimationStrategy implements IEstimationStrategy
{
    public function __construct(
        private readonly ImageStorageService $storage,
    ) {}

    public function estimate(Bovino $bovino, WeightEstimationRequest $request, int $capturedBy): Fotografia
    {
        $path = $this->storage->upload($request->image, 'fotografias');

        $fotografia = Fotografia::create([
            'bovino_id' => $bovino->id,
            'capturada_por' => $capturedBy,
            'ruta_archivo' => $path,
            'estado_procesamiento' => 'pendiente',
            'capturada_el' => now(),
        ]);

        ProcessWeightEstimationJob::dispatch($fotografia->id, $request->breedConstant);

        return $fotografia;
    }
}
