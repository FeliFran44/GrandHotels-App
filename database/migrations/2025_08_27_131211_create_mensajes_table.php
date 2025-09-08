<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversacion_id')->constrained('conversaciones')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Quién envió el mensaje
            $table->text('cuerpo');
            $table->timestamp('leido_a')->nullable(); // Para la confirmación de lectura
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('mensajes');
    }
};