<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            // 1. Eliminamos la antigua clave foránea específica
            $table->dropForeign(['comunicado_id']);
            $table->dropColumn('comunicado_id');

            // 2. Añadimos las columnas polimórficas
            $table->morphs('archivable'); // Esto crea archivable_id y archivable_type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archivos', function (Blueprint $table) {
            $table->dropMorphs('archivable');
            $table->foreignId('comunicado_id')->constrained('comunicados');
        });
    }
};