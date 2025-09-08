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
    Schema::create('personal_seguridad', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('hotel_id'); // Columna para guardar el ID del hotel al que pertenece
        $table->string('nombre');
        $table->string('apellido');
        $table->string('puesto'); // Ej: "Guardia", "Supervisor"
        $table->string('turno'); // Ej: "Mañana", "Tarde", "Noche"
        $table->boolean('activo')->default(true); // Para saber si está trabajando actualmente o no
        $table->timestamps();

        // Aquí creamos la relación (Llave Foránea)
        $table->foreign('hotel_id')->references('id')->on('hoteles')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_seguridad');
    }
};
