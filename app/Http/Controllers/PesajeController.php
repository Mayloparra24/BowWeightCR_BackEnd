<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CorrectPesajeRequest;
use App\Http\Requests\StorePesajeRequest;
use App\Models\Bovino;
use App\Models\RegistroPesaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PesajeController extends Controller
{
    public function index(Request $request, int $bovinoId): JsonResponse
    {
        $bovino = Bovino::findOrFail($bovinoId);

        $pesajes = $bovino->pesajes()
            ->with(['fotografia', 'creadoPor'])
            ->orderByDesc('registrado_el')
            ->paginate($request->input('per_page', 15));

        return response()->json($pesajes);
    }

    public function store(StorePesajeRequest $request): JsonResponse
    {
        $pesaje = RegistroPesaje::create([
            'bovino_id' => $request->validated('bovino_id'),
            'fotografia_id' => null,
            'creado_por' => $request->user()?->id ?? 1,
            'peso_estimado' => null,
            'peso_registrado' => $request->validated('peso_registrado'),
            'es_correccion_manual' => false,
            'tipo_pesaje' => 'manual',
            'notas_correccion' => $request->validated('notas_correccion'),
            'confianza_ia' => null,
            'registrado_el' => now(),
        ]);

        return response()->json([
            'mensaje' => 'Pesaje manual registrado correctamente.',
            'data' => $pesaje->load('bovino'),
        ], 201);
    }

    public function correct(CorrectPesajeRequest $request, int $id): JsonResponse
    {
        $pesaje = RegistroPesaje::findOrFail($id);

        $pesaje->update([
            'peso_registrado' => $request->validated('peso_registrado'),
            'notas_correccion' => $request->validated('notas_correccion'),
            'es_correccion_manual' => true,
        ]);

        return response()->json([
            'mensaje' => 'Pesaje corregido correctamente.',
            'data' => $pesaje->fresh(),
        ]);
    }
}
