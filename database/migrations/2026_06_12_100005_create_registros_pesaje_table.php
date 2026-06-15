<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_pesaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bovino_id')->constrained('bovinos');
            $table->foreignId('fotografia_id')->nullable()->constrained('fotografias')->nullOnDelete();
            $table->foreignId('creado_por')->constrained('usuarios');
            $table->decimal('peso_estimado', 8, 2)->nullable();
            $table->decimal('peso_registrado', 8, 2)->nullable();
            $table->boolean('es_correccion_manual')->default(false);
            $table->enum('tipo_pesaje', ['ia', 'manual']);
            $table->text('notas_correccion')->nullable();
            $table->decimal('confianza_ia', 5, 4)->nullable();
            $table->timestamp('registrado_el')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_pesaje');
    }
};
