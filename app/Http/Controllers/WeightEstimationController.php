<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\WeightEstimationRequest;
use App\Exceptions\WeightEstimationException;
use App\Http\Requests\EstimateWeightRequest;
use App\Models\Bovino;
use App\Models\Fotografia;
use App\Models\RegistroPesaje;
use App\Services\ImageStorageService;
use App\Services\WeightEstimationOrchestrator;
use Illuminate\Http\JsonResponse;

class WeightEstimationController extends Controller
{
    public function estimate(EstimateWeightRequest $request): JsonResponse
    {
        $bovino = Bovino::findOrFail($request->validated('bovino_id'));

        $dto = new WeightEstimationRequest(
            image: $request->file('foto'),
            breedConstant: (float) $request->validated('constante_raza'),
        );

        $orchestrator = new WeightEstimationOrchestrator(new ImageStorageService);

        try {
            $resultado = $orchestrator->estimate(
                bovino: $bovino,
                request: $dto,
                capturedBy: $request->user()?->id ?? 1,
                forceOffline: (bool) $request->validated('modo_offline', false),
            );
        } catch (WeightEstimationException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                ],
            ], $e->getCode() ?: 422);
        }

        if ($resultado instanceof Fotografia) {
            return response()->json([
                'success' => true,
                'mensaje' => 'Fotografía recibida. Se procesará cuando haya conexión.',
                'data' => [
                    'fotografia_id' => $resultado->id,
                    'estado' => $resultado->estado_procesamiento,
                ],
            ], 202);
        }

        /** @var RegistroPesaje $resultado */
        return response()->json([
            'success' => true,
            'mensaje' => 'Peso estimado correctamente.',
            'data' => [
                'pesaje_id' => $resultado->id,
                'peso_estimado_kg' => $resultado->peso_estimado,
                'confianza_yolo' => $resultado->confianza_ia,
            ],
        ]);
    }
}
