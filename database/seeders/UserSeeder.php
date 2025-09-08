<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Crear Coordinador ---
        User::create([
            'name' => 'Ernesto',
            'email' => 'coordinador@ghl.com',
            'password' => Hash::make('admin123'),
            'rol' => 'Coordinador',
            'hotel_id' => null,
        ]);

        // --- Crear Gerentes ---
        $theGrand = Hotel::where('nombre', 'The Grand Hotel')->first();
        if ($theGrand) {
            User::create([
                'name' => 'thegrand',
                'email' => 'thegrand@ghl.com',
                'password' => Hash::make('grandhotel123'),
                'rol' => 'Gerente',
                'hotel_id' => $theGrand->id,
            ]);
        }

        $panoramic = Hotel::where('nombre', 'Panoramic Grand')->first();
        if ($panoramic) {
            User::create([
                'name' => 'panoramic',
                'email' => 'panoramic@ghl.com',
                'password' => Hash::make('panoramicgrand123'),
                'rol' => 'Gerente',
                'hotel_id' => $panoramic->id,
            ]);
        }

        $iguazu = Hotel::where('nombre', 'Iguazu Grand')->first();
        if ($iguazu) {
            User::create([
                'name' => 'iguazu',
                'email' => 'iguazu@ghl.com',
                'password' => Hash::make('iguazugrand123'),
                'rol' => 'Gerente',
                'hotel_id' => $iguazu->id,
            ]);
        }

        $recoleta = Hotel::where('nombre', 'Recoleta Grand')->first();
        if ($recoleta) {
            User::create([
                'name' => 'recoleta',
                'email' => 'recoleta@ghl.com',
                'password' => Hash::make('recoletagrand123'),
                'rol' => 'Gerente',
                'hotel_id' => $recoleta->id,
            ]);
        }

        // --- Crear Usuario Fantasma (solo lectura) ---
        if (!User::where('email', 'fantasma@ghl.com')->exists()) {
            User::create([
                'name' => 'Fantasma',
                'email' => 'fantasma@ghl.com',
                'password' => Hash::make('sololectura123'),
                'rol' => 'Fantasma',
                'hotel_id' => null,
            ]);
        }
    }
}
