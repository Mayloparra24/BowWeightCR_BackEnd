<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\IEstimationStrategy;
use App\DTOs\WeightEstimationRequest;
use App\Models\Bovino;

class WeightEstimationContext
{
    private IEstimationStrategy $strategy;

    public function setStrategy(IEstimationStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function estimate(Bovino $bovino, WeightEstimationRequest $request, int $capturedBy): mixed
    {
        return $this->strategy->estimate($bovino, $request, $capturedBy);
    }
}
