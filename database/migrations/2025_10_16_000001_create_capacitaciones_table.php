<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacitaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hoteles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo'); // Seguridad, Primeros Auxilios, Manejo de Crisis, etc.
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('instructor')->nullable();
            $table->text('participantes')->nullable(); // Nombres o cantidad
            $table->string('estado')->default('Planificada'); // Planificada, En Curso, Completada, Cancelada
            $table->text('resultados')->nullable(); // Observaciones/resultados post-capacitación
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitaciones');
    }
};
