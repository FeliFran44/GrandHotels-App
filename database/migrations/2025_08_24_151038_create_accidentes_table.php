<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accidentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hoteles');
            $table->foreignId('user_id')->constrained('users');
            $table->string('tipo'); // "Accidente" o "Incidente"
            $table->dateTime('fecha_evento');
            $table->text('descripcion');
            $table->text('involucrados')->nullable();
            $table->text('acciones_tomadas')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('accidentes');
    }
};