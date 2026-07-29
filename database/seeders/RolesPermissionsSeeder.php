<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Admin',     'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Recepcion', 'descripcion' => 'Gestión de clientes y órdenes de servicio'],
            ['nombre' => 'Mecanico',  'descripcion' => 'Actualización de estado de órdenes asignadas'],
            ['nombre' => 'Bodega',    'descripcion' => 'Gestión de inventario y repuestos'],
            ['nombre' => 'Contador',  'descripcion' => 'Acceso a pagos, facturas y reportes'],
            ['nombre' => 'Cliente',   'descripcion' => 'Portal de cliente — citas, vehículos y seguimiento'],
        ];

        DB::table('roles')->insertOrIgnore(array_map(fn($r) => array_merge($r, [
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]), $roles));

        $modulos = [
            'clientes'   => ['ver', 'crear', 'editar', 'eliminar'],
            'vehiculos'  => ['ver', 'crear', 'editar', 'eliminar'],
            'ordenes'    => ['ver', 'crear', 'editar', 'eliminar', 'cambiar_estado'],
            'inventario' => ['ver', 'crear', 'editar', 'eliminar', 'movimientos'],
            'pagos'      => ['ver', 'crear', 'confirmar', 'reembolsar'],
            'facturas'   => ['ver', 'crear', 'emitir', 'anular'],
            'mecanicos'  => ['ver', 'crear', 'editar', 'eliminar'],
            'reportes'   => ['ver', 'exportar'],
            'usuarios'   => ['ver', 'crear', 'editar', 'eliminar'],
            'roles'      => ['ver', 'asignar'],
        ];

        $permisos = [];
        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $permisos[] = [
                    'nombre'     => "{$modulo}.{$accion}",
                    'modulo'     => $modulo,
                    'accion'     => $accion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('permissions')->insertOrIgnore($permisos);
    }
}
