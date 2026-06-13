<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Adapters\MLServiceAdapter;
use App\DTOs\WeightEstimationRequest;
use App\Exceptions\WeightEstimationException;
use App\Models\Fotografia;
use App\Models\RegistroPesaje;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessWeightEstimationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly int $fotografiaId,
        public readonly float $breedConstant,
    ) {}

    public function handle(): void
    {
        $fotografia = Fotografia::findOrFail($this->fotografiaId);
        $fotografia->marcarComoProcesando();

        $tempPath = null;

        try {
            $disk = Storage::disk(config('filesystems.default', 'local'));
            $tempPath = sys_get_temp_dir().'/'.basename($fotografia->ruta_archivo);

            file_put_contents($tempPath, $disk->get($fotografia->ruta_archivo));

            $uploadedFile = new UploadedFile(
                $tempPath,
                basename($fotografia->ruta_archivo),
                $disk->mimeType($fotografia->ruta_archivo),
                null,
                true
            );

            $request = new WeightEstimationRequest($uploadedFile, $this->breedConstant);
            $adapter = MLServiceAdapter::fromConfig();
            $result = $adapter->estimar($request);

            $fotografia->marcarComoCompletado();

            RegistroPesaje::create([
                'bovino_id' => $fotografia->bovino_id,
                'fotografia_id' => $fotografia->id,
                'creado_por' => $fotografia->capturada_por,
                'peso_estimado' => $result->estimatedWeight,
                'peso_registrado' => $result->estimatedWeight,
                'tipo_pesaje' => 'ia',
                'confianza_ia' => $result->confidence,
                'registrado_el' => now(),
            ]);
        } catch (WeightEstimationException $e) {
            $fotografia->marcarComoFallido();
            $this->fail($e);
        } catch (Throwable $e) {
            $fotografia->marcarComoFallido();
            throw $e;
        } finally {
            if ($tempPath !== null && file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
