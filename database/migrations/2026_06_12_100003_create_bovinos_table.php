<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bovinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finca_id')->constrained('fincas');
            $table->foreignId('raza_id')->constrained('razas');
            $table->string('numero_arete')->unique();
            $table->string('nombre_animal')->nullable();
            $table->enum('sexo', ['macho', 'hembra']);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('motivo_inactividad')->nullable();
            $table->date('fecha_inactividad')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bovinos');
    }
};
