<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AsignacionVeterinario;
use App\Models\Finca;
use App\Models\Usuario;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsignacionVeterinarioController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Veterinarios asignados obtenidos correctamente.', 'data' => [
            ['id' => 1, 'veterinario_id' => 2, 'finca_id' => 1, 'esta_activa' => true, 'asignado_el' => '2026-06-17T00:00:00+00:00', 'veterinario' => ['id' => 2, 'nombre_completo' => 'Dr. Roberto Solano', 'correo_electronico' => 'veterinario@bovweight.com', 'rol' => 'veterinario']],
        ]], status: 200,
    )]
    public function index(Request $request, int $fincaId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $this->authorize('viewAny', [AsignacionVeterinario::class, $finca]);

        $asignaciones = $finca->asignaciones()
            ->with('veterinario')
            ->where('esta_activa', true)
            ->get();

        return ApiResponse::success(
            data: $asignaciones,
            message: 'Veterinarios asignados obtenidos correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Veterinario asignado correctamente.', 'data' => [
            'id' => 1, 'veterinario_id' => 2, 'finca_id' => 1, 'esta_activa' => true, 'asignado_el' => '2026-06-17T00:00:00+00:00', 'veterinario' => ['id' => 2, 'nombre_completo' => 'Dr. Roberto Solano', 'correo_electronico' => 'veterinario@bovweight.com', 'rol' => 'veterinario'],
        ]], status: 201,
    )]
    public function store(Request $request, int $fincaId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $this->authorize('create', [AsignacionVeterinario::class, $finca]);

        $data = $request->validate([
            'veterinario_id' => ['required', 'exists:usuarios,id'],
        ]);

        $veterinario = Usuario::findOrFail($data['veterinario_id']);

        if (! $veterinario->esVeterinario()) {
            return ApiResponse::error(
                message: 'El usuario seleccionado no es veterinario.',
                status: 422,
            );
        }

        $yaAsignado = AsignacionVeterinario::where('veterinario_id', $data['veterinario_id'])
            ->where('finca_id', $fincaId)
            ->where('esta_activa', true)
            ->exists();

        if ($yaAsignado) {
            return ApiResponse::error(
                message: 'Este veterinario ya está asignado a esta finca.',
                status: 422,
            );
        }

        $asignacion = AsignacionVeterinario::create([
            'veterinario_id' => $data['veterinario_id'],
            'finca_id' => $fincaId,
            'asignado_por' => $request->user()->id,
            'esta_activa' => true,
        ]);

        return ApiResponse::success(
            data: $asignacion->load('veterinario'),
            message: 'Veterinario asignado correctamente.',
            status: 201,
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Veterinario removido de la finca correctamente.', 'data' => null], status: 200,
    )]
    public function destroy(Request $request, int $fincaId, int $asignacionId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $asignacion = AsignacionVeterinario::where('id', $asignacionId)
            ->where('finca_id', $fincaId)
            ->firstOrFail();

        $this->authorize('delete', $asignacion);

        $asignacion->update(['esta_activa' => false]);

        return ApiResponse::success(
            message: 'Veterinario removido de la finca correctamente.',
        );
    }
}
