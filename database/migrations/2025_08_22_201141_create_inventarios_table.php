<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hoteles')->onDelete('cascade');
            $table->string('nombre');
            $table->string('estado');
            $table->date('ultima_fecha_mantenimiento')->nullable();
            $table->date('proxima_fecha_mantenimiento')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('inventario');
    }
};