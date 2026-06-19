<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('finca_id')->constrained('fincas');
            $table->foreignId('asignado_por')->constrained('usuarios');
            $table->string('rol', 20);
            $table->boolean('esta_activa')->default(true);
            $table->timestamp('asignado_el')->useCurrent();
            $table->timestamps();

            $table->unique(['usuario_id', 'finca_id', 'rol']);
        });

        DB::statement(<<<SQL
            INSERT INTO asignaciones (usuario_id, finca_id, asignado_por, rol, esta_activa, asignado_el, created_at, updated_at)
            SELECT veterinario_id, finca_id, asignado_por, 'veterinario', esta_activa, asignado_el, created_at, updated_at
            FROM asignaciones_veterinarios
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
