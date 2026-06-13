<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bovino_id')->constrained('bovinos');
            $table->foreignId('capturada_por')->constrained('usuarios');
            $table->string('ruta_archivo', 500);
            $table->enum('estado_procesamiento', ['pendiente', 'procesando', 'completado', 'fallido'])->default('pendiente');
            $table->timestamp('capturada_el')->useCurrent();
            $table->timestamp('sincronizada_el')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografias');
    }
};
