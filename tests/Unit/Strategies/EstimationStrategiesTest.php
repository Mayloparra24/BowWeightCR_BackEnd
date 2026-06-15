<?php

declare(strict_types=1);

namespace Tests\Unit\Strategies;

use App\Contracts\IEstimadorPeso;
use App\DTOs\WeightEstimationRequest;
use App\DTOs\WeightEstimationResult;
use App\Jobs\ProcessWeightEstimationJob;
use App\Models\Bovino;
use App\Models\Finca;
use App\Models\Raza;
use App\Models\Usuario;
use App\Services\ImageStorageService;
use App\Strategies\OfflineEstimationStrategy;
use App\Strategies\OnlineEstimationStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EstimationStrategiesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function online_strategy_creates_weight_record(): void
    {
        Storage::fake('local');
        Queue::fake();

        $estimator = $this->createMock(IEstimadorPeso::class);
        $estimator->method('estimar')->willReturn(new WeightEstimationResult(450.0, 0.9));

        $storage = new ImageStorageService('local');
        $strategy = new OnlineEstimationStrategy($estimator, $storage);

        $user = Usuario::factory()->create();
        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create();
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $file = UploadedFile::fake()->image('bovino.jpg');
        $request = new WeightEstimationRequest($file, 250.0);

        $strategy->estimate($bovino, $request, $user->id);

        $this->assertDatabaseHas('registros_pesaje', [
            'bovino_id' => $bovino->id,
            'peso_estimado' => 450.0,
            'confianza_ia' => 0.9,
            'tipo_pesaje' => 'ia',
        ]);

        $this->assertDatabaseHas('fotografias', [
            'bovino_id' => $bovino->id,
            'estado_procesamiento' => 'completado',
        ]);
    }

    #[Test]
    public function offline_strategy_queues_job_and_creates_pending_photo(): void
    {
        Storage::fake('local');
        Queue::fake();

        $storage = new ImageStorageService('local');
        $strategy = new OfflineEstimationStrategy($storage);

        $user = Usuario::factory()->create();
        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create();
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $file = UploadedFile::fake()->image('bovino.jpg');
        $request = new WeightEstimationRequest($file, 250.0);

        $fotografia = $strategy->estimate($bovino, $request, $user->id);

        $this->assertEquals('pendiente', $fotografia->estado_procesamiento);
        $this->assertDatabaseHas('fotografias', [
            'id' => $fotografia->id,
            'estado_procesamiento' => 'pendiente',
        ]);
        Queue::assertPushed(ProcessWeightEstimationJob::class);
    }
}
