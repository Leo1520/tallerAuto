<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de servicio
        DB::table('tipos_servicio')->insertOrIgnore([
            ['nombre' => 'Mantenimiento',  'descripcion' => 'Servicios de mantenimiento preventivo y correctivo', 'activo' => true],
            ['nombre' => 'Diagnóstico',    'descripcion' => 'Diagnóstico de fallas mecánicas y eléctricas', 'activo' => true],
            ['nombre' => 'Mecánica',       'descripcion' => 'Reparaciones mecánicas generales', 'activo' => true],
            ['nombre' => 'Electricidad',   'descripcion' => 'Instalaciones y reparaciones eléctricas', 'activo' => true],
            ['nombre' => 'Carrocería',     'descripcion' => 'Chapistería y pintura', 'activo' => true],
            ['nombre' => 'Suspensión',     'descripcion' => 'Frenos, suspensión y dirección', 'activo' => true],
            ['nombre' => 'Aire acondic.',  'descripcion' => 'Sistema de aire acondicionado', 'activo' => true],
        ]);

        // Métodos de pago
        DB::table('metodos_pago')->insertOrIgnore([
            ['nombre' => 'Efectivo',        'requiere_referencia' => false, 'activo' => true, 'comision' => 0],
            ['nombre' => 'Tarjeta débito',  'requiere_referencia' => true,  'activo' => true, 'comision' => 0],
            ['nombre' => 'Tarjeta crédito', 'requiere_referencia' => true,  'activo' => true, 'comision' => 3.00],
            ['nombre' => 'Transferencia',   'requiere_referencia' => true,  'activo' => true, 'comision' => 0],
            ['nombre' => 'QR',              'requiere_referencia' => true,  'activo' => true, 'comision' => 0],
            ['nombre' => 'Stripe',          'requiere_referencia' => true,  'activo' => true, 'comision' => 2.90],
        ]);

        // Especialidades de mecánicos
        DB::table('especialidades')->insertOrIgnore([
            ['nombre' => 'Mecánica general',    'descripcion' => 'Motor, transmisión y tren motriz'],
            ['nombre' => 'Electricidad',         'descripcion' => 'Sistemas eléctricos y electrónicos'],
            ['nombre' => 'Carrocería y pintura', 'descripcion' => 'Chapistería, pintura y acabados'],
            ['nombre' => 'Suspensión y frenos',  'descripcion' => 'Sistema de frenos, suspensión y dirección'],
            ['nombre' => 'Aire acondicionado',   'descripcion' => 'Sistemas de climatización'],
            ['nombre' => 'Diagnóstico',          'descripcion' => 'Escaneo y diagnóstico computarizado'],
        ]);

        // Marcas y modelos
        $marcas = [
            'Toyota'     => ['Corolla', 'Hilux', 'RAV4', 'Fortuner', 'Yaris', 'Prado'],
            'Chevrolet'  => ['Spark', 'Sail', 'Captiva', 'Trailblazer', 'Colorado'],
            'Nissan'     => ['Sentra', 'Frontier', 'X-Trail', 'Navara', 'Qashqai'],
            'Hyundai'    => ['Accent', 'Tucson', 'Santa Fe', 'Elantra', 'i10'],
            'Kia'        => ['Picanto', 'Rio', 'Sportage', 'Sorento', 'Seltos'],
            'Ford'       => ['Ranger', 'Explorer', 'F-150', 'EcoSport', 'Bronco'],
            'Volkswagen' => ['Gol', 'Polo', 'Golf', 'Amarok', 'Tiguan'],
            'Honda'      => ['Civic', 'CR-V', 'HR-V', 'Accord', 'Pilot'],
            'Suzuki'     => ['Swift', 'Vitara', 'Jimny', 'Grand Vitara'],
            'Mitsubishi' => ['L200', 'Outlander', 'Montero', 'Eclipse Cross'],
        ];

        foreach ($marcas as $nombreMarca => $modelos) {
            $marcaId = DB::table('marcas')->insertGetId([
                'nombre' => $nombreMarca,
                'activo' => true,
            ]);

            $modelosInsert = array_map(fn($m) => [
                'marca_id' => $marcaId,
                'nombre'   => $m,
                'activo'   => true,
            ], $modelos);

            DB::table('modelos')->insertOrIgnore($modelosInsert);
        }
    }
}
