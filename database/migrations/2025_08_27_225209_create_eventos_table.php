<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hoteles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->string('tipo'); // Social, Corporativo, etc.
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->integer('capacidad_esperada')->nullable();
            $table->integer('capacidad_maxima')->nullable();
            $table->text('necesidades_seguridad')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('eventos');
    }
};