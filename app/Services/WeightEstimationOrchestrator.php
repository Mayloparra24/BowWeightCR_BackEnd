<?php

declare(strict_types=1);

namespace App\Services;

use App\Adapters\MLServiceAdapter;
use App\DTOs\WeightEstimationRequest;
use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\RegistroPesaje;
use App\Strategies\OfflineEstimationStrategy;
use App\Strategies\OnlineEstimationStrategy;

class WeightEstimationOrchestrator
{
    public function __construct(
        private readonly ImageStorageService $storage,
    ) {}

    public function estimate(
        Bovino $bovino,
        WeightEstimationRequest $request,
        int $capturedBy,
        bool $forceOffline = false,
    ): RegistroPesaje|Fotografia {
        $context = new WeightEstimationContext;

        if ($forceOffline || ! $this->isOnline()) {
            $context->setStrategy(new OfflineEstimationStrategy($this->storage));
        } else {
            $context->setStrategy(new OnlineEstimationStrategy(
                MLServiceAdapter::fromConfig(),
                $this->storage,
            ));
        }

        return $context->estimate($bovino, $request, $capturedBy);
    }

    private function isOnline(): bool
    {
        // Por defecto asumimos online. El frontend puede forzar modo offline
        // cuando detecta que no hay conectividad.
        return true;
    }
}
