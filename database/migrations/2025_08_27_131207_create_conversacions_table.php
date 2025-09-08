<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            // Necesitamos saber los dos participantes de la conversación
            $table->foreignId('participante_uno_id')->constrained('users');
            $table->foreignId('participante_dos_id')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('conversaciones');
    }
};