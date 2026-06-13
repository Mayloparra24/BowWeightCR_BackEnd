<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('accion');
            $table->string('entidad_tipo')->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('direccion_ip', 45)->nullable();
            $table->timestamp('creada_el')->useCurrent();

            $table->index(['usuario_id']);
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index(['creada_el']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_actividades');
    }
};
