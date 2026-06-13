<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\WeightEstimationRequest;
use App\Exceptions\WeightEstimationException;
use App\Models\Bovino;

interface IEstimationStrategy
{
    /**
     * Ejecuta la estimación de peso según la estrategia implementada.
     *
     * @throws WeightEstimationException
     */
    public function estimate(Bovino $bovino, WeightEstimationRequest $request, int $capturedBy): mixed;
}
