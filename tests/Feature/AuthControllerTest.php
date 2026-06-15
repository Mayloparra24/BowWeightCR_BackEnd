<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_login_with_valid_credentials(): void
    {
        Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena_hash' => 'secret123',
            'esta_activo' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token', 'usuario'],
            ]);
    }

    #[Test]
    public function user_cannot_login_with_invalid_password(): void
    {
        Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena_hash' => 'secret123',
            'esta_activo' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'wrongpassword',
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function inactive_user_cannot_login(): void
    {
        Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena_hash' => 'secret123',
            'esta_activo' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'secret123',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = Usuario::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[Test]
    public function authenticated_user_can_retrieve_own_profile(): void
    {
        $user = Usuario::factory()->create([
            'nombre_completo' => 'Test User',
            'correo_electronico' => 'test@example.com',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('data.nombre_completo', 'Test User')
            ->assertJsonPath('data.correo_electronico', 'test@example.com');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }
}
