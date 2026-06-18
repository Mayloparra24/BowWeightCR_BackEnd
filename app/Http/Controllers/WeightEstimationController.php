<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\WeightEstimationRequest as WeightEstimationDto;
use App\Exceptions\WeightEstimationException;
use App\Http\Requests\EstimateWeightRequest;
use App\Http\Resources\PesajeResource;
use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\Raza;
use App\Models\RegistroPesaje;
use App\Services\ImageStorageService;
use App\Services\WeightEstimationOrchestrator;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WeightEstimationController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Peso estimado correctamente.', 'data' => [
            'id' => 1, 'bovino_id' => 1, 'peso_registrado' => null, 'peso_estimado' => 425.3, 'peso_final' => 425.3, 'tipo_pesaje' => 'ia', 'es_correccion_manual' => false, 'confianza_ia' => 0.92, 'registrado_el' => '2026-06-17T10:00:00+00:00',
            'bovino' => ['id' => 1, 'numero_arete' => '1234567890', 'nombre_animal' => 'Torito'],
        ]], status: 200,
    )]
    public function estimate(EstimateWeightRequest $request): JsonResponse
    {
        $bovino = Bovino::findOrFail($request->validated('bovino_id'));

        $this->authorize('create', [\App\Models\RegistroPesaje::class, $bovino]);

        $raza = Raza::findOrFail($request->validated('raza_id'));

        $dto = new WeightEstimationDto(
            image: $request->file('foto'),
            breedConstant: (float) $raza->constante_peso,
        );

        $orchestrator = new WeightEstimationOrchestrator(new ImageStorageService);

        try {
            $resultado = $orchestrator->estimate(
                bovino: $bovino,
                request: $dto,
                capturedBy: $request->user()->id,
                forceOffline: (bool) $request->validated('modo_offline', false),
            );
        } catch (WeightEstimationException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                status: $e->getCode() ?: 422,
                code: $e->getErrorCode(),
            );
        }

        if ($resultado instanceof Fotografia) {
            return ApiResponse::success(
                data: [
                    'fotografia_id' => $resultado->id,
                    'estado' => $resultado->estado_procesamiento,
                ],
                message: 'Fotografía recibida. Se procesará cuando haya conexión.',
                status: 202,
            );
        }

        /** @var RegistroPesaje $resultado */
        return ApiResponse::resource(
            resource: new PesajeResource($resultado->load('bovino')),
            message: 'Peso estimado correctamente.',
        );
    }
}
