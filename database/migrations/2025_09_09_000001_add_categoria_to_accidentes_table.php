<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('accidentes', function (Blueprint $table) {
            $table->string('categoria')->nullable()->after('tipo');
            $table->index('categoria');
        });
    }
    public function down(): void {
        Schema::table('accidentes', function (Blueprint $table) {
            $table->dropIndex(['categoria']);
            $table->dropColumn('categoria');
        });
    }
};

