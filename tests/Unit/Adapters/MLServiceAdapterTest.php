<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use App\Adapters\MLServiceAdapter;
use App\DTOs\WeightEstimationRequest;
use App\Exceptions\WeightEstimationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MLServiceAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ml_service' => [
                'base_url' => 'https://unranked-mystified-thesaurus.ngrok-free.dev',
                'endpoint' => '/api/v1/predict-weight',
                'timeout' => 15,
                'headers' => ['ngrok-skip-browser-warning' => 'true'],
            ],
        ]);
    }

    #[Test]
    public function it_returns_estimation_result_on_success(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'peso_estimado_kg' => 450.32,
                    'confianza_yolo' => 0.9234,
                ],
            ], 200),
        ]);

        $adapter = MLServiceAdapter::fromConfig();
        $file = UploadedFile::fake()->image('bovino.jpg');
        $request = new WeightEstimationRequest($file, 250.0);

        $result = $adapter->estimar($request);

        $this->assertEquals(450.32, $result->estimatedWeight);
        $this->assertEquals(0.9234, $result->confidence);
    }

    #[Test]
    public function it_throws_exception_on_service_error(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => false,
                'error' => [
                    'code' => 'NO_BOVINE_DETECTED',
                    'message' => 'No se detecto la silueta del bovino en la imagen.',
                ],
            ], 422),
        ]);

        $adapter = MLServiceAdapter::fromConfig();
        $file = UploadedFile::fake()->image('bovino.jpg');
        $request = new WeightEstimationRequest($file, 250.0);

        $this->expectException(WeightEstimationException::class);

        $adapter->estimar($request);
    }

    #[Test]
    public function it_throws_exception_on_connection_error(): void
    {
        Http::fake(function () {
            throw new ConnectionException(
                'cURL error 6: Could not resolve host'
            );
        });

        $adapter = MLServiceAdapter::fromConfig();
        $file = UploadedFile::fake()->image('bovino.jpg');
        $request = new WeightEstimationRequest($file, 250.0);

        $this->expectException(WeightEstimationException::class);

        $adapter->estimar($request);
    }
}
