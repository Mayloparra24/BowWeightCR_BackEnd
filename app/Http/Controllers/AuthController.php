<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Usuario;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'correo_electronico' => ['required', 'email'],
            'contrasena' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('correo_electronico', $request->correo_electronico)->first();

        if (! $usuario || ! Hash::check($request->contrasena, $usuario->contrasena_hash)) {
            return ApiResponse::error(
                message: 'Credenciales incorrectas.',
                status: 401,
            );
        }

        if (! $usuario->esta_activo) {
            return ApiResponse::error(
                message: 'Tu cuenta está desactivada. Contactá al administrador.',
                status: 403,
            );
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'token' => $token,
                'usuario' => new UserResource($usuario),
            ],
            message: 'Sesión iniciada correctamente.',
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(
            message: 'Sesión cerrada correctamente.',
        );
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::resource(
            resource: new UserResource($request->user()),
            message: 'Usuario autenticado obtenido correctamente.',
        );
    }
}