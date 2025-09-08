    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('operaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->constrained('hoteles')->onDelete('cascade');
                // Más adelante asociaremos esto con el usuario que registra la novedad. Por ahora lo dejamos simple.
                // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('categoria'); // Incidente, Accidente, Mantenimiento, Novedad General
                $table->string('prioridad'); // Baja, Media, Alta, Crítica
                $table->text('descripcion');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('operaciones');
        }
    };
    