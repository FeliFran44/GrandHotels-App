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
    Schema::create('hoteles', function (Blueprint $table) {
        $table->id(); // Crea una columna 'id' autoincremental (Llave Primaria)
        $table->string('nombre'); // Columna para el nombre del hotel (Ej: "The Grand Hotel")
        $table->string('ubicacion')->nullable(); // Columna para la ubicación (Ej: "Punta del Este"). La hacemos opcional.
        $table->timestamps(); // Crea las columnas 'created_at' y 'updated_at' automáticamente
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoteles');
    }
};
