<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\AsignacionVeterinario;
use App\Models\Finca;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsignacionVeterinarioController extends Controller
{
    public function index(Request $request, int $fincaId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $usuario = $request->user();
        if (! $usuario->esAdministrador() && $finca->propietario_id !== $usuario->id) {
            return response()->json(['mensaje' => 'No tenés acceso a esta finca.'], 403);
        }

        $asignaciones = $finca->asignaciones()
            ->with('veterinario')
            ->where('esta_activa', true)
            ->get();

        return response()->json($asignaciones);
    }

    public function store(Request $request, int $fincaId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $usuario = $request->user();
        if (! $usuario->esGanadero() || $finca->propietario_id !== $usuario->id) {
            return response()->json(['mensaje' => 'Solo el dueño de la finca puede asignar veterinarios.'], 403);
        }

        $data = $request->validate([
            'veterinario_id' => ['required', 'exists:usuarios,id'],
        ]);

        $veterinario = Usuario::findOrFail($data['veterinario_id']);
        if (! $veterinario->esVeterinario()) {
            return response()->json(['mensaje' => 'El usuario seleccionado no es veterinario.'], 422);
        }

        $yaAsignado = AsignacionVeterinario::where('veterinario_id', $data['veterinario_id'])
            ->where('finca_id', $fincaId)
            ->where('esta_activa', true)
            ->exists();

        if ($yaAsignado) {
            return response()->json(['mensaje' => 'Este veterinario ya está asignado a esta finca.'], 422);
        }

        $asignacion = AsignacionVeterinario::create([
            'veterinario_id' => $data['veterinario_id'],
            'finca_id'       => $fincaId,
            'asignado_por'   => $usuario->id,
            'esta_activa'    => true,
        ]);

        return response()->json([
            'mensaje' => 'Veterinario asignado correctamente.',
            'data'    => $asignacion->load('veterinario'),
        ], 201);
    }

    public function destroy(Request $request, int $fincaId, int $asignacionId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $usuario = $request->user();
        if (! $usuario->esGanadero() || $finca->propietario_id !== $usuario->id) {
            return response()->json(['mensaje' => 'Solo el dueño de la finca puede remover veterinarios.'], 403);
        }

        $asignacion = AsignacionVeterinario::where('id', $asignacionId)
            ->where('finca_id', $fincaId)
            ->firstOrFail();

        $asignacion->update(['esta_activa' => false]);

        return response()->json([
            'mensaje' => 'Veterinario removido de la finca correctamente.',
        ]);
    }
}