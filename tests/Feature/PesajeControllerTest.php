<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bovino;
use App\Models\Finca;
use App\Models\Raza;
use App\Models\RegistroPesaje;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PesajeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_weight_history_for_a_bovine(): void
    {
        $user = Usuario::factory()->create();
        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create();
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        RegistroPesaje::factory()->count(3)->create([
            'bovino_id' => $bovino->id,
            'creado_por' => $user->id,
        ]);

        $response = $this->getJson("/api/bovinos/{$bovino->id}/pesajes");

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function it_stores_a_manual_weight_record(): void
    {
        $user = Usuario::factory()->create();
        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create();
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $response = $this->postJson('/api/pesajes', [
            'bovino_id' => $bovino->id,
            'peso_registrado' => 420.50,
            'notas_correccion' => 'Pesaje manual de control',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.peso_registrado', '420.50')
            ->assertJsonPath('data.tipo_pesaje', 'manual');

        $this->assertDatabaseHas('registros_pesaje', [
            'bovino_id' => $bovino->id,
            'peso_registrado' => 420.50,
            'tipo_pesaje' => 'manual',
        ]);
    }

    #[Test]
    public function it_corrects_an_existing_weight_record(): void
    {
        $user = Usuario::factory()->create();
        $finca = Finca::factory()->create(['propietario_id' => $user->id]);
        $raza = Raza::factory()->create();
        $bovino = Bovino::factory()->create(['finca_id' => $finca->id, 'raza_id' => $raza->id]);

        $pesaje = RegistroPesaje::factory()->create([
            'bovino_id' => $bovino->id,
            'creado_por' => $user->id,
            'peso_estimado' => 380.00,
            'peso_registrado' => 380.00,
            'tipo_pesaje' => 'ia',
        ]);

        $response = $this->putJson("/api/pesajes/{$pesaje->id}/corregir", [
            'peso_registrado' => 410.00,
            'notas_correccion' => 'Corrección por báscula',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.peso_registrado', '410.00')
            ->assertJsonPath('data.es_correccion_manual', true);

        $this->assertDatabaseHas('registros_pesaje', [
            'id' => $pesaje->id,
            'peso_registrado' => 410.00,
            'es_correccion_manual' => true,
        ]);
    }
}
