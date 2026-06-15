<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Resources\BovinoResource;
use App\Models\Bovino;
use App\Models\Finca;
use App\Support\ApiResponse;
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

        return ApiResponse::resource(
            resource: BovinoResource::collection($bovinos),
            message: 'Bovinos obtenidos correctamente.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Bovino::class);

        $data = $request->validate([
            'finca_id' => ['required', 'exists:fincas,id'],
            'raza_id' => ['required', 'exists:razas,id'],
            'numero_arete' => ['required', 'string', 'max:100', 'unique:bovinos,numero_arete'],
            'nombre_animal' => ['nullable', 'string', 'max:255'],
            'sexo' => ['required', 'in:macho,hembra'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'notas' => ['nullable', 'string'],
        ]);

        $finca = Finca::findOrFail($data['finca_id']);

        $this->authorize('view', $finca);

        $bovino = Bovino::create($data);

        return ApiResponse::resource(
            resource: new BovinoResource($bovino->load(['finca', 'raza'])),
            message: 'Bovino registrado correctamente.',
            status: 201,
        );
    }

    public function show(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('view', $bovino);

        return ApiResponse::resource(
            resource: new BovinoResource($bovino->load(['finca', 'raza', 'pesajes'])),
            message: 'Bovino obtenido correctamente.',
        );
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

        return ApiResponse::resource(
            resource: new BovinoResource($bovino->fresh()->load(['finca', 'raza'])),
            message: 'Bovino actualizado correctamente.',
        );
    }

    public function destroy(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('delete', $bovino);

        $bovino->delete();

        return ApiResponse::success(
            message: 'Bovino eliminado correctamente.',
        );
    }

    public function marcarInactivo(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('update', $bovino);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
        ]);

        if (! $bovino->estaActivo()) {
            return ApiResponse::error(
                message: 'El bovino ya está inactivo.',
                status: 422,
            );
        }

        $bovino->marcarInactivo($data['motivo']);

        return ApiResponse::resource(
            resource: new BovinoResource($bovino->fresh()->load(['finca', 'raza'])),
            message: 'Bovino marcado como inactivo.',
        );
    }

    public function marcarActivo(Request $request, Bovino $bovino): JsonResponse
    {
        $this->authorize('update', $bovino);

        if ($bovino->estaActivo()) {
            return ApiResponse::error(
                message: 'El bovino ya está activo.',
                status: 422,
            );
        }

        $bovino->marcarActivo();

        return ApiResponse::resource(
            resource: new BovinoResource($bovino->fresh()->load(['finca', 'raza'])),
            message: 'Bovino reactivado correctamente.',
        );
    }
}