<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\InventarioSucursal;
use App\Models\Mecanico;
use App\Models\MetodoPago;
use App\Models\Modelo;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\Persona;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\TipoServicio;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal  = Sucursal::first();
        $adminUser = User::first();

        // ── Servicios ─────────────────────────────────────────────
        $tipoMec  = TipoServicio::where('nombre', 'Mecánica')->first();
        $tipoMant = TipoServicio::where('nombre', 'Mantenimiento')->first();

        $serviciosData = [
            ['nombre' => 'Cambio de aceite y filtro',    'precio' => 120,  'tipo' => $tipoMant],
            ['nombre' => 'Alineación y balanceo',         'precio' => 180,  'tipo' => $tipoMec],
            ['nombre' => 'Revisión de frenos',            'precio' => 250,  'tipo' => $tipoMec],
            ['nombre' => 'Diagnóstico computarizado',     'precio' => 150,  'tipo' => $tipoMant],
            ['nombre' => 'Cambio de pastillas de freno',  'precio' => 320,  'tipo' => $tipoMec],
            ['nombre' => 'Revisión general 50.000 km',    'precio' => 480,  'tipo' => $tipoMant],
        ];

        foreach ($serviciosData as $s) {
            Servicio::firstOrCreate(['nombre' => $s['nombre']], [
                'tipo_servicio_id' => $s['tipo']?->id,
                'precio'           => $s['precio'],
                'activo'           => true,
            ]);
        }

        // ── Proveedor y repuestos ──────────────────────────────────
        $proveedor = Proveedor::firstOrCreate(['nombre' => 'AutoPartes Bolivia'], [
            'telefono' => '4-4512345',
            'email'    => 'ventas@autopartes.bo',
            'ciudad'   => 'Cochabamba',
            'activo'   => true,
        ]);

        $repuestosData = [
            ['nombre' => 'Filtro de aceite Bosch',     'codigo' => 'FIL-001', 'compra' => 45,  'venta' => 85,  'stock' => 20, 'min' => 5],
            ['nombre' => 'Aceite Mobil 10W-40 (lt)',   'codigo' => 'ACE-001', 'compra' => 38,  'venta' => 65,  'stock' => 50, 'min' => 10],
            ['nombre' => 'Pastillas freno delanteras',  'codigo' => 'PAS-001', 'compra' => 120, 'venta' => 220, 'stock' => 3,  'min' => 5],
            ['nombre' => 'Filtro de aire Toyota',      'codigo' => 'FAI-001', 'compra' => 55,  'venta' => 110, 'stock' => 8,  'min' => 3],
            ['nombre' => 'Bujías NGK (juego 4)',       'codigo' => 'BUJ-001', 'compra' => 85,  'venta' => 160, 'stock' => 2,  'min' => 4],
        ];

        foreach ($repuestosData as $r) {
            $rep = Repuesto::firstOrCreate(['codigo' => $r['codigo']], [
                'proveedor_id'  => $proveedor->id,
                'nombre'        => $r['nombre'],
                'precio_compra' => $r['compra'],
                'precio_venta'  => $r['venta'],
                'activo'        => true,
            ]);
            InventarioSucursal::firstOrCreate(
                ['sucursal_id' => $sucursal->id, 'repuesto_id' => $rep->id],
                ['stock' => $r['stock'], 'stock_minimo' => $r['min'], 'updated_at' => now()]
            );
        }

        // ── Mecánicos ─────────────────────────────────────────────
        $esp = Especialidad::first();
        $mecanicosData = [
            ['nombre' => 'Carlos Mamani',   'cedula' => '3456789', 'salario' => 4500],
            ['nombre' => 'Pedro Quispe',    'cedula' => '5678901', 'salario' => 4200],
            ['nombre' => 'Luis Condori',    'cedula' => '7890123', 'salario' => 3800],
        ];

        $mecanicoObjs = [];
        foreach ($mecanicosData as $m) {
            $email   = strtolower(str_replace(' ', '.', $m['nombre'])) . '@tallerpro.bo';
            $persona = Persona::firstOrCreate(['email' => $email], [
                'nombre'   => $m['nombre'],
                'telefono' => '7' . rand(1000000, 9999999),
            ]);
            $mecanicoObjs[] = Mecanico::firstOrCreate(['cedula' => $m['cedula']], [
                'persona_id'      => $persona->id,
                'sucursal_id'     => $sucursal->id,
                'especialidad_id' => $esp?->id,
                'salario'         => $m['salario'],
                'activo'          => true,
            ]);
        }

        // ── Clientes y vehículos ───────────────────────────────────
        $clientes = [
            ['nombre' => 'Juan Carlos Flores',    'doc' => 'CI-1234567', 'tel' => '72345678', 'placa' => '1234ABC'],
            ['nombre' => 'Maria Elena Gutierrez', 'doc' => 'CI-2345678', 'tel' => '73456789', 'placa' => '2345BCD'],
            ['nombre' => 'Roberto Sanchez Lopez', 'doc' => 'CI-3456789', 'tel' => '74567890', 'placa' => '3456CDE'],
            ['nombre' => 'Ana Patricia Morales',  'doc' => 'CI-4567890', 'tel' => '75678901', 'placa' => '4567DEF'],
        ];

        $vehiculoObjs = [];
        foreach ($clientes as $idx => $c) {
            $email   = strtolower(str_replace(' ', '.', $c['nombre'])) . '@gmail.com';
            $persona = Persona::firstOrCreate(['email' => $email], [
                'nombre'   => $c['nombre'],
                'telefono' => $c['tel'],
            ]);
            $clienteModel = \App\Models\Cliente::firstOrCreate(['persona_id' => $persona->id], [
                'numero_documento' => $c['doc'],
                'tipo_documento'   => 'CI',
                'ciudad'           => 'Cochabamba',
            ]);
            $modeloRand = Modelo::inRandomOrder()->first();
            $veh = \App\Models\Vehiculo::firstOrCreate(['placa' => $c['placa']], [
                'cliente_id'  => $clienteModel->id,
                'modelo_id'   => $modeloRand?->id,
                'vin'         => strtoupper(substr(md5($c['placa']), 0, 17)),
                'ano'         => rand(2016, 2023),
                'color'       => collect(['Blanco','Negro','Rojo','Gris','Azul'])->random(),
                'kilometraje' => rand(15000, 120000),
                'activo'      => true,
            ]);
            $vehiculoObjs[] = ['vehiculo' => $veh, 'cliente' => $clienteModel];
        }

        // ── Órdenes de servicio ────────────────────────────────────
        $servicioObj = Servicio::first();
        $estados     = ['En reparación', 'Listo', 'Entregado', 'Entregado'];
        $metodoPago  = MetodoPago::where('nombre', 'Efectivo')->first();

        foreach ($vehiculoObjs as $idx => $pair) {
            $estado = $estados[$idx];
            $mec    = $mecanicoObjs[$idx % count($mecanicoObjs)];
            $dias   = -($idx * 5 + rand(1, 4));

            $orden = OrdenServicio::create([
                'vehiculo_id'            => $pair['vehiculo']->id,
                'mecanico_id'            => $mec->id,
                'sucursal_id'            => $sucursal->id,
                'numero'                 => 'OS-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'estado'                 => $estado,
                'prioridad'              => collect(['Baja','Media','Alta'])->random(),
                'subtotal'               => 600,
                'descuento'              => 0,
                'impuestos'              => 78,
                'total'                  => 678,
                'fecha_ingreso'          => now()->addDays($dias),
                'fecha_entrega_estimada' => now()->addDays($dias + 3),
                'fecha_entrega_real'     => $estado === 'Entregado' ? now()->addDays($dias + 2) : null,
                'observaciones'          => 'Orden demo #' . ($idx + 1),
            ]);

            DB::table('detalle_orden_servicio')->insert([
                'orden_id'          => $orden->id,
                'servicio_id'       => $servicioObj->id,
                'cantidad'          => 1,
                'precio_unitario'   => 600,
                'subtotal'          => 600,
                'estado'            => $estado === 'Entregado' ? 'Completado' : 'Pendiente',
            ]);

            if ($estado === 'Entregado') {
                Pago::create([
                    'orden_id'       => $orden->id,
                    'metodo_pago_id' => $metodoPago?->id,
                    'user_id'        => $adminUser->id,
                    'monto'          => 678,
                    'moneda'         => 'BOB',
                    'estado'         => 'Confirmado',
                    'created_at'     => now()->addDays($dias + 2),
                    'updated_at'     => now()->addDays($dias + 2),
                ]);
            }
        }
    }
}
