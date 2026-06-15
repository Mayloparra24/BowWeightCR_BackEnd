<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fincas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->constrained('usuarios');
            $table->string('nombre_finca');
            $table->string('ubicacion')->nullable();
            $table->string('canton')->nullable();
            $table->string('provincia')->nullable();
            $table->boolean('esta_activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fincas');
    }
};
