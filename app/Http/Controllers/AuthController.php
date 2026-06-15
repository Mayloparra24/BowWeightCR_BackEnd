<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Usuario;
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
            return response()->json([
                'mensaje' => 'Credenciales incorrectas.',
            ], 401);
        }

        if (! $usuario->esta_activo) {
            return response()->json([
                'mensaje' => 'Tu cuenta está desactivada. Contactá al administrador.',
            ], 403);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Sesión iniciada correctamente.',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre_completo,
                'correo' => $usuario->correo_electronico,
                'rol' => $usuario->rol,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}