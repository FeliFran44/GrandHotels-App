<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_seguridad_id')->constrained('personal_seguridad')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vacaciones');
    }
};