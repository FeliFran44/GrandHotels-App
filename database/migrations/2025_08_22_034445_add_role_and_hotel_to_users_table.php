<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Definimos la clase con el nombre que Laravel está buscando
class AddRoleAndHotelToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Columna para el rol: Coordinador o Gerente
            $table->string('rol')->after('email'); 
            
            // Columna para el ID del hotel, puede ser nula (para el Coordinador)
            $table->foreignId('hotel_id')->nullable()->after('rol')->constrained('hoteles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Esto permite deshacer la migración si es necesario
            $table->dropForeign(['hotel_id']);
            $table->dropColumn(['rol', 'hotel_id']);
        });
    }
}
