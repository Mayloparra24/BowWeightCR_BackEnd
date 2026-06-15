<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

readonly class WeightEstimationRequest
{
    public function __construct(
        public UploadedFile $image,
        public float $breedConstant,
    ) {}
}
