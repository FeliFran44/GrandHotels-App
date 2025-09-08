<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_seguridad', function (Blueprint $table) {
            // Añadimos las nuevas columnas para el horario
            $table->time('hora_entrada')->nullable()->after('turno');
            $table->time('hora_salida')->nullable()->after('hora_entrada');

            // Eliminamos la columna 'activo' que ya no usaremos
            $table->dropColumn('activo');
        });
    }

    public function down(): void
    {
        Schema::table('personal_seguridad', function (Blueprint $table) {
            // Esto permite revertir los cambios si fuera necesario
            $table->dropColumn(['hora_entrada', 'hora_salida']);
            $table->boolean('activo')->default(true);
        });
    }
};