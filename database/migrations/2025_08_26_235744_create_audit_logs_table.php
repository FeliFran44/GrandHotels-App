<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // Ej: "created", "updated", "deleted"
            $table->string('model_type'); // Ej: "App\Models\PersonalSeguridad"
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable(); // Guardará el "antes" en formato JSON
            $table->json('new_values')->nullable(); // Guardará el "después" en formato JSON
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('audit_logs');
    }
};