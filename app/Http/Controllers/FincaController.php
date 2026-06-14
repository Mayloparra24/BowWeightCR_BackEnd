<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Finca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->esAdministrador()) {
            $fincas = Finca::with('propietario')->get();
        } elseif ($usuario->esVeterinario()) {
            $fincas = Finca::whereHas('asignaciones', function ($query) use ($usuario) {
                $query->where('veterinario_id', $usuario->id)
                    ->where('esta_activa', true);
            })->with('propietario')->get();
        } else {
            $fincas = Finca::where('propietario_id', $usuario->id)
                ->with('propietario')
                ->get();
        }

        return response()->json($fincas);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Finca::class);

        $data = $request->validate([
            'nombre_finca' => ['required', 'string', 'max:255'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],
            'canton'       => ['nullable', 'string', 'max:100'],
            'provincia'    => ['nullable', 'string', 'max:100'],
        ]);

        $finca = Finca::create([
            ...$data,
            'propietario_id' => $request->user()->id,
        ]);

        return response()->json([
            'mensaje' => 'Finca registrada correctamente.',
            'data'    => $finca,
        ], 201);
    }

    public function show(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('view', $finca);

        return response()->json($finca->load(['propietario', 'bovinos']));
    }

    public function update(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('update', $finca);

        $data = $request->validate([
            'nombre_finca' => ['sometimes', 'string', 'max:255'],
            'ubicacion'    => ['nullable', 'string', 'max:255'],
            'canton'       => ['nullable', 'string', 'max:100'],
            'provincia'    => ['nullable', 'string', 'max:100'],
            'esta_activa'  => ['sometimes', 'boolean'],
        ]);

        $finca->update($data);

        return response()->json([
            'mensaje' => 'Finca actualizada correctamente.',
            'data'    => $finca->fresh(),
        ]);
    }

    public function destroy(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('delete', $finca);

        $finca->delete();

        return response()->json([
            'mensaje' => 'Finca eliminada correctamente.',
        ]);
    }
}