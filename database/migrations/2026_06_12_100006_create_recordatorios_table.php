<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('bovino_id')->nullable()->constrained('bovinos')->nullOnDelete();
            $table->foreignId('finca_id')->nullable()->constrained('fincas')->nullOnDelete();
            $table->string('titulo');
            $table->enum('tipo_frecuencia', ['diaria', 'semanal', 'mensual', 'personalizada']);
            $table->timestamp('proximo_recordatorio_el');
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordatorios');
    }
};
