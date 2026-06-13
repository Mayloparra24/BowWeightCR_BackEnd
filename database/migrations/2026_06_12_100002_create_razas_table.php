<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('razas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_raza')->unique();
            $table->enum('enfoque', ['carne', 'doble_proposito', 'leche']);
            $table->decimal('constante_peso', 8, 2);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('razas');
    }
};
