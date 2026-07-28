<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sucursales')->insertOrIgnore([
            [
                'nombre'    => 'Sucursal Central',
                'direccion' => 'Av. Heroínas #450',
                'ciudad'    => 'Cochabamba',
                'telefono'  => '4-4501234',
                'email'     => 'central@tallerpro.bo',
                'latitud'   => -17.3935,
                'longitud'  => -66.1570,
                'activo'    => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre'    => 'Sucursal Norte',
                'direccion' => 'Av. Blanco Galindo Km 5',
                'ciudad'    => 'Cochabamba',
                'telefono'  => '4-4512345',
                'email'     => 'norte@tallerpro.bo',
                'latitud'   => -17.3650,
                'longitud'  => -66.2010,
                'activo'    => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
