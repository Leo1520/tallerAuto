<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $personaId = DB::table('persona')->insertGetId([
            'nombre'     => 'Administrador Taller Automotrices SC-BOL',
            'telefono'   => '70000000',
            'email'      => 'leonardopenaanez@gmail.com',
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'persona_id'        => $personaId,
            'email'             => 'leonardopenaanez@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('L.l1234567'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $adminRolId = DB::table('roles')->where('nombre', 'Admin')->value('id');

        if ($adminRolId) {
            DB::table('role_user')->insertOrIgnore([
                'role_id' => $adminRolId,
                'user_id' => $userId,
            ]);

            // Asignar todos los permisos al rol Admin
            $permisos = DB::table('permissions')->pluck('id');
            foreach ($permisos as $permisoId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permisoId,
                    'role_id'       => $adminRolId,
                ]);
            }
        }
    }
}
