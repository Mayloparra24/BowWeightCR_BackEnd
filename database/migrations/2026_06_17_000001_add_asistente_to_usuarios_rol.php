<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Las nuevas instalaciones ya crean la columna con 'asistente'.
            // SQLite en tests usa migraciones frescas, por lo que no se requiere alteración.
            return;
        }

        $existing = collect(DB::select(
            "SELECT COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'usuarios'
               AND COLUMN_NAME = 'rol'"
        ))->first();

        if ($existing && str_contains($existing->COLUMN_TYPE, "'asistente'")) {
            return;
        }

        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('administrador','ganadero','veterinario','asistente') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('administrador','ganadero','veterinario') NOT NULL");
    }
};
