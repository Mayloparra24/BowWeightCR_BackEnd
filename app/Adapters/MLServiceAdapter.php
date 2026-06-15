<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Contracts\IEstimadorPeso;
use App\DTOs\WeightEstimationRequest;
use App\DTOs\WeightEstimationResult;
use App\Exceptions\WeightEstimationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MLServiceAdapter implements IEstimadorPeso
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $endpoint,
        private readonly int $timeout,
        private readonly array $headers,
    ) {}

    public static function fromConfig(): self
    {
        $config = config('services.ml_service');

        return new self(
            baseUrl: rtrim($config['base_url'], '/'),
            endpoint: $config['endpoint'],
            timeout: (int) $config['timeout'],
            headers: $config['headers'] ?? [],
        );
    }

    public function estimar(WeightEstimationRequest $request): WeightEstimationResult
    {
        $url = $this->baseUrl.'/'.ltrim($this->endpoint, '/');

        try {
            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->attach(
                    'file',
                    fopen($request->image->getRealPath(), 'r'),
                    $request->image->getClientOriginalName()
                )
                ->post($url, [
                    'constante_raza' => $request->breedConstant,
                ]);
        } catch (ConnectionException $e) {
            throw new WeightEstimationException(
                message: 'No se pudo conectar con el servicio de inteligencia artificial. Verifique que el microservicio esté activo.',
                errorCode: 'ML_SERVICE_UNAVAILABLE',
                previous: $e,
            );
        }

        if ($response->failed()) {
            $this->handleErrorResponse($response);
        }

        $data = $response->json('data');

        return new WeightEstimationResult(
            estimatedWeight: (float) $data['peso_estimado_kg'],
            confidence: (float) $data['confianza_yolo'],
        );
    }

    private function handleErrorResponse(Response $response): never
    {
        $status = $response->status();
        $errorCode = $response->json('error.code', 'INTERNAL_ERROR');
        $message = $response->json('error.message', 'Error inesperado del servicio de inteligencia artificial.');

        throw new WeightEstimationException(
            message: $message,
            errorCode: $errorCode,
            response: $response,
            code: $status,
        );
    }
}
