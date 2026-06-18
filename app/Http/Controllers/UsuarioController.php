<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Usuario;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $usuarios = Usuario::orderBy('nombre_completo')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated(
            paginator: $usuarios,
            message: 'Usuarios obtenidos correctamente.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $data = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'correo_electronico' => ['required', 'email', 'unique:usuarios,correo_electronico'],
            'contrasena' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'string', 'in:administrador,ganadero,veterinario,asistente'],
            'esta_activo' => ['sometimes', 'boolean'],
        ]);

        $usuario = Usuario::create([
            'nombre_completo' => $data['nombre_completo'],
            'correo_electronico' => $data['correo_electronico'],
            'contrasena_hash' => $data['contrasena'],
            'rol' => $data['rol'],
            'esta_activo' => $data['esta_activo'] ?? true,
        ]);

        return ApiResponse::resource(
            resource: new UserResource($usuario),
            message: 'Usuario creado correctamente.',
            status: 201,
        );
    }

    public function show(Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        return ApiResponse::resource(
            resource: new UserResource($usuario),
            message: 'Usuario obtenido correctamente.',
        );
    }

    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $data = $request->validate([
            'nombre_completo' => ['sometimes', 'string', 'max:255'],
            'correo_electronico' => ['sometimes', 'email', Rule::unique('usuarios', 'correo_electronico')->ignore($usuario->id)],
            'contrasena' => ['sometimes', 'string', 'min:8'],
            'rol' => ['sometimes', 'string', 'in:administrador,ganadero,veterinario,asistente'],
            'esta_activo' => ['sometimes', 'boolean'],
        ]);

        $updateData = [
            'nombre_completo' => $data['nombre_completo'] ?? $usuario->nombre_completo,
            'correo_electronico' => $data['correo_electronico'] ?? $usuario->correo_electronico,
            'rol' => $data['rol'] ?? $usuario->rol,
            'esta_activo' => $data['esta_activo'] ?? $usuario->esta_activo,
        ];

        if (isset($data['contrasena'])) {
            $updateData['contrasena_hash'] = Hash::make($data['contrasena']);
        }

        $usuario->update($updateData);

        return ApiResponse::resource(
            resource: new UserResource($usuario->fresh()),
            message: 'Usuario actualizado correctamente.',
        );
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        if ($usuario->id === request()->user()->id) {
            return ApiResponse::error(
                message: 'No podés eliminar tu propio usuario.',
                status: 422,
            );
        }

        $usuario->delete();

        return ApiResponse::success(
            message: 'Usuario eliminado correctamente.',
        );
    }
}
