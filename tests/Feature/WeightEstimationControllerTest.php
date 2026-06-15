<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ProcessWeightEstimationJob;
use App\Models\Bovino;
use App\Models\Finca;
use App\Models\Raza;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WeightEstimationControllerTest extends TestCase
{
    use RefreshDatabase;

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
    public function it_estimates_weight_online_and_returns_result(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        Queue::fake();

        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'peso_estimado_kg' => 450.50,
                    'confianza_yolo' => 0.92,
                ],
            ], 200),
        ]);

        $user = Usuario::factory()->create();
        Sanctum::actingAs($user);

        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create(['constante_peso' => 250]);
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $response = $this->postJson('/api/pesajes/estimar', [
            'bovino_id' => $bovino->id,
            'foto' => UploadedFile::fake()->image('bovino.jpg'),
            'raza_id' => $raza->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.peso_estimado', 450.5)
            ->assertJsonPath('data.confianza_ia', 0.92);

        $this->assertDatabaseHas('registros_pesaje', [
            'bovino_id' => $bovino->id,
            'peso_estimado' => 450.50,
            'tipo_pesaje' => 'ia',
        ]);
    }

    #[Test]
    public function it_queues_weight_estimation_when_offline_mode_is_requested(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        Queue::fake();

        $user = Usuario::factory()->create();
        Sanctum::actingAs($user);

        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create(['constante_peso' => 250]);
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $response = $this->postJson('/api/pesajes/estimar', [
            'bovino_id' => $bovino->id,
            'foto' => UploadedFile::fake()->image('bovino.jpg'),
            'raza_id' => $raza->id,
            'modo_offline' => true,
        ]);

        $response->assertAccepted()
            ->assertJsonPath('data.estado', 'pendiente');

        Queue::assertPushed(ProcessWeightEstimationJob::class);
    }

    #[Test]
    public function it_returns_error_when_ml_service_fails(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        Queue::fake();

        Http::fake([
            '*' => Http::response([
                'success' => false,
                'error' => [
                    'code' => 'NO_BOVINE_DETECTED',
                    'message' => 'No se detecto la silueta del bovino en la imagen.',
                ],
            ], 422),
        ]);

        $user = Usuario::factory()->create();
        Sanctum::actingAs($user);

        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create(['constante_peso' => 250]);
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $response = $this->postJson('/api/pesajes/estimar', [
            'bovino_id' => $bovino->id,
            'foto' => UploadedFile::fake()->image('bovino.jpg'),
            'raza_id' => $raza->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('error.code', 'NO_BOVINE_DETECTED');
    }
}
