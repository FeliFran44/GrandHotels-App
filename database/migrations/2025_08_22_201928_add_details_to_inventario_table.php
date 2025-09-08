<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->string('marca_modelo')->nullable()->after('nombre');
            $table->string('ubicacion_exacta')->nullable()->after('marca_modelo');
            $table->date('fecha_compra')->nullable()->after('ubicacion_exacta');
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropColumn(['marca_modelo', 'ubicacion_exacta', 'fecha_compra']);
        });
    }
};