<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class WeightEstimationResult
{
    public function __construct(
        public float $estimatedWeight,
        public float $confidence,
    ) {}
}
