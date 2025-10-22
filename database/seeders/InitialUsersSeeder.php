<?php
namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Coordinador
        User::updateOrCreate(
            ['email' => 'coordinador@ghl.com'],
            [
                'name' => 'Ernesto (Coordinador)',
                'password' => Hash::make('admin123'),
                'rol' => 'Coordinador',
                'hotel_id' => null,
                'permisos' => ['hoteles','personal','comunicados','inventario','accidentes','planificacion','reportes','chat','archivo_general','auditoria'],
            ]
        );

        // Gerentes por hotel (intenta encontrarlos por nombre)
        $map = [
            'The Grand Hotel' => ['email' => 'thegrand@ghl.com','name' => 'Gerente The Grand','pass' => 'grandhotel123'],
            'Panoramic Grand' => ['email' => 'panoramic@ghl.com','name' => 'Gerente Panoramic','pass' => 'panoramicgrand123'],
            'Iguazu Grand'    => ['email' => 'iguazu@ghl.com','name' => 'Gerente Iguazu','pass' => 'iguazugrand123'],
            'Recoleta Grand'  => ['email' => 'recoleta@ghl.com','name' => 'Gerente Recoleta','pass' => 'recoletagrand123'],
        ];
        foreach ($map as $hotelNombre => $data) {
            $hotel = Hotel::where('nombre', $hotelNombre)->first();
            if (!$hotel) { continue; }
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['pass']),
                    'rol' => 'Gerente',
                    'hotel_id' => $hotel->id,
                    'permisos' => ['personal','comunicados','inventario','accidentes','planificacion','chat','reportes'],
                ]
            );
        }
    }
}

