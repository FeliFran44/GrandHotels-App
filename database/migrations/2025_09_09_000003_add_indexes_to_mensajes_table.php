<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mensajes', function (Blueprint $table) {
            $table->index(['conversacion_id', 'created_at']);
            $table->index('user_id');
            $table->index('leido_a');
        });
    }
    public function down(): void {
        Schema::table('mensajes', function (Blueprint $table) {
            $table->dropIndex(['mensajes_conversacion_id_created_at_index']);
            $table->dropIndex(['mensajes_user_id_index']);
            $table->dropIndex(['mensajes_leido_a_index']);
        });
    }
};

