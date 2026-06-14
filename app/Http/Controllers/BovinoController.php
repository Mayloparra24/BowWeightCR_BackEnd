<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Bovino;
use App\Models\Finca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BovinoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->esAdministrador()) {
            $bovinos = Bovino::with(['finca', 'raza'])->get();
        } elseif ($usuario->esVeterinario()) {
            $bovinos = Bovino::whereHas('finca.asignaciones', function ($query) use ($usuario) {
                $query->where('veterinario_id', $usuario->id)
                    ->where('esta_activa', true);
            })->with(['finca', 'raza'])->get();
        } else {
            $bovinos = Bovino::whereHas('finca', function ($query) use ($usuario) {
                $query->where('propietario_id', $usuario->id);
            })->with(['finca', 'raza'])->get();
        }

        return response()->json($bovinos);
    }

    public function store(Request $request): JsonResponse
{
    $usuario = $request->user();

    if (! $usuario->esGanadero()) {
        return response()->json([
            'mensaje' => 'Solo los ganaderos pueden registrar bovinos.',
        ], 403);
    }

    $data = $request->validate([
        'finca_id'         => ['required', 'exists:fincas,id'],
        'raza_id'          => ['required', 'exists:razas,id'],
        'numero_arete'     => ['required', 'string', 'max:100', 'unique:bovinos,numero_arete'],
        'nombre_animal'    => ['nullable', 'string', 'max:255'],
        'sexo'             => ['required', 'in:macho,hembra'],
        'fecha_nacimiento' => ['nullable', 'date'],
        'notas'            => ['nullable', 'string'],
    ]);

    $finca = Finca::findOrFail($data['finca_id']);

    if ($finca->propietario_id !== $usuario->id) {
        return response()->json([
            'mensaje' => 'No podés registrar bovinos en una finca que no es tuya.',
        ], 403);
    }

    $bovino = Bovino::create($data);

    return response()->json([
        'mensaje' => 'Bovino registrado correctamente.',
        'data'    => $bovino->load(['finca', 'raza']),
    ], 201);
}

    public function show(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('view', $bovino);

        return response()->json($bovino->load(['finca', 'raza', 'pesajes']));
    }

    public function update(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('update', $bovino);

        $data = $request->validate([
            'raza_id'          => ['sometimes', 'exists:razas,id'],
            'nombre_animal'    => ['nullable', 'string', 'max:255'],
            'sexo'             => ['sometimes', 'in:macho,hembra'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'notas'            => ['nullable', 'string'],
        ]);

        $bovino->update($data);

        return response()->json([
            'mensaje' => 'Bovino actualizado correctamente.',
            'data'    => $bovino->fresh()->load(['finca', 'raza']),
        ]);
    }

    public function destroy(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('delete', $bovino);

        $bovino->delete();

        return response()->json([
            'mensaje' => 'Bovino eliminado correctamente.',
        ]);
    }

    public function marcarInactivo(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('update', $bovino);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        if (! $bovino->estaActivo()) {
            return response()->json([
                'mensaje' => 'El bovino ya está inactivo.',
            ], 422);
        }

        $bovino->marcarInactivo($data['motivo']);

        return response()->json([
            'mensaje' => 'Bovino marcado como inactivo.',
            'data'    => $bovino->fresh(),
        ]);
    }

    public function marcarActivo(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('update', $bovino);

        if ($bovino->estaActivo()) {
            return response()->json([
                'mensaje' => 'El bovino ya está activo.',
            ], 422);
        }

        $bovino->marcarActivo();

        return response()->json([
            'mensaje' => 'Bovino reactivado correctamente.',
            'data'    => $bovino->fresh(),
        ]);
    }
}