<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Añadimos índices a las columnas que más usaremos para filtrar
            $table->index('user_id');
            $table->index(['model_type', 'model_id']);
        });
    }
    public function down(): void {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['model_type', 'model_id']);
        });
    }
};