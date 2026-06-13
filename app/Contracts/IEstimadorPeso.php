<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\WeightEstimationRequest;
use App\DTOs\WeightEstimationResult;
use App\Exceptions\WeightEstimationException;

interface IEstimadorPeso
{
    /**
     * Estima el peso de un bovino a partir de una imagen.
     *
     * @throws WeightEstimationException
     */
    public function estimar(WeightEstimationRequest $request): WeightEstimationResult;
}
