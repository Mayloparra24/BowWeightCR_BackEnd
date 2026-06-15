<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CorrectPesajeRequest;
use App\Http\Requests\StorePesajeRequest;
use App\Http\Resources\PesajeResource;
use App\Models\Bovino;
use App\Models\RegistroPesaje;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PesajeController extends Controller
{
    public function index(Request $request, int $bovinoId): JsonResponse
    {
        $bovino = Bovino::findOrFail($bovinoId);

        $this->authorize('viewAny', [RegistroPesaje::class, $bovino]);

        $pesajes = $bovino->pesajes()
            ->with(['fotografia', 'creadoPor'])
            ->orderByDesc('registrado_el')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated(
            paginator: $pesajes,
            message: 'Pesajes obtenidos correctamente.',
        );
    }

    public function store(StorePesajeRequest $request): JsonResponse
    {
        $bovino = Bovino::findOrFail($request->validated('bovino_id'));

        $this->authorize('create', [RegistroPesaje::class, $bovino]);

        $pesaje = RegistroPesaje::create([
            'bovino_id' => $bovino->id,
            'fotografia_id' => null,
            'creado_por' => $request->user()->id,
            'peso_estimado' => null,
            'peso_registrado' => $request->validated('peso_registrado'),
            'es_correccion_manual' => false,
            'tipo_pesaje' => 'manual',
            'notas_correccion' => $request->validated('notas_correccion'),
            'confianza_ia' => null,
            'registrado_el' => now(),
        ]);

        return ApiResponse::resource(
            resource: new PesajeResource($pesaje->load('bovino')),
            message: 'Pesaje manual registrado correctamente.',
            status: 201,
        );
    }

    public function correct(CorrectPesajeRequest $request, int $id): JsonResponse
    {
        $pesaje = RegistroPesaje::with('bovino')->findOrFail($id);

        $this->authorize('update', $pesaje);

        $pesaje->update([
            'peso_registrado' => $request->validated('peso_registrado'),
            'notas_correccion' => $request->validated('notas_correccion'),
            'es_correccion_manual' => true,
        ]);

        return ApiResponse::resource(
            resource: new PesajeResource($pesaje->fresh()),
            message: 'Pesaje corregido correctamente.',
        );
    }
}
