<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capacitaciones', function (Blueprint $table) {
            // Eliminar columnas que ya no se usan
            $table->dropColumn(['fecha_fin', 'estado']);
            
            // Agregar nueva columna para duración aproximada
            $table->string('duracion_aproximada')->nullable()->after('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('capacitaciones', function (Blueprint $table) {
            // Restaurar columnas eliminadas
            $table->dateTime('fecha_fin')->nullable();
            $table->string('estado')->default('Planificada');
            
            // Eliminar columna agregada
            $table->dropColumn('duracion_aproximada');
        });
    }
};
