<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        Hotel::create(['nombre' => 'The Grand Hotel', 'ubicacion' => 'Punta del Este', 'color' => '#0d6efd']); // Azul
        Hotel::create(['nombre' => 'Panoramic Grand', 'ubicacion' => 'Puerto Iguazú', 'color' => '#198754']); // Verde
        Hotel::create(['nombre' => 'Iguazu Grand', 'ubicacion' => 'Puerto Iguazú', 'color' => '#ffc107']); // Amarillo
        Hotel::create(['nombre' => 'Recoleta Grand', 'ubicacion' => 'Buenos Aires', 'color' => '#dc3545']); // Rojo
    }
}