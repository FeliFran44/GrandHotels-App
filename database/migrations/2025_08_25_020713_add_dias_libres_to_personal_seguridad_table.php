<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('personal_seguridad', function (Blueprint $table) {
            $table->string('dias_libres')->nullable()->after('hora_salida');
        });
    }
    public function down(): void {
        Schema::table('personal_seguridad', function (Blueprint $table) {
            $table->dropColumn('dias_libres');
        });
    }
};