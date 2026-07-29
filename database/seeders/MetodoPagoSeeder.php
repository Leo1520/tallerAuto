<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo',          'requiere_referencia' => false, 'activo' => true, 'comision' => null],
            ['nombre' => 'QR Banco Ganadero', 'requiere_referencia' => false, 'activo' => true, 'comision' => null],
            ['nombre' => 'Stripe / Tarjeta',  'requiere_referencia' => false, 'activo' => true, 'comision' => null],
        ];

        foreach ($metodos as $data) {
            MetodoPago::firstOrCreate(['nombre' => $data['nombre']], $data);
        }
    }
}
