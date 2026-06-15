<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_veterinarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinario_id')->constrained('usuarios');
            $table->foreignId('finca_id')->constrained('fincas');
            $table->foreignId('asignado_por')->constrained('usuarios');
            $table->boolean('esta_activa')->default(true);
            $table->timestamp('asignado_el')->useCurrent();
            $table->timestamps();

            $table->unique(['veterinario_id', 'finca_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_veterinarios');
    }
};
