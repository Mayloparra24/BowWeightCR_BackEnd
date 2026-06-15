<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\IEstimationStrategy;
use App\DTOs\WeightEstimationRequest;
use App\Models\Bovino;
use InvalidArgumentException;

class WeightEstimationContext
{
    private ?IEstimationStrategy $strategy = null;

    public function setStrategy(IEstimationStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function estimate(Bovino $bovino, WeightEstimationRequest $request, int $capturedBy): mixed
    {
        if ($this->strategy === null) {
            throw new InvalidArgumentException('No se ha configurado una estrategia de estimación.');
        }

        return $this->strategy->estimate($bovino, $request, $capturedBy);
    }
}
