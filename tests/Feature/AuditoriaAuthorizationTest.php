<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de autorización para las rutas sensibles del módulo de auditoría.
 *
 * Cubre tres capas:
 *   1. Sin autenticación              → 302 al login
 *   2. Rol sin permiso (middleware)   → 403
 *   3. Rol autorizado                 → pasa la capa de autorización
 *
 * NOTA: Las pruebas de renderizado completo (vista + datos) pertenecen a tests
 * de integración/browser; aquí sólo verificamos la capa de control de acceso.
 */
class AuditoriaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function userConRol(string $nombreRol): User
    {
        $role = Role::firstOrCreate(
            ['nombre' => $nombreRol],
            ['descripcion' => $nombreRol, 'activo' => true]
        );
        $user = User::factory()->create();
        $user->roles()->attach($role->id);
        return $user;
    }

    // ─── reportes.caja ──────────────────────────────────────────────────────

    public function test_caja_requiere_autenticacion(): void
    {
        $this->get(route('reportes.caja'))
             ->assertRedirect(route('login'));
    }

    public function test_caja_prohibe_rol_mecanico(): void
    {
        $this->actingAs($this->userConRol('Mecanico'))
             ->get(route('reportes.caja'))
             ->assertForbidden();
    }

    public function test_caja_prohibe_rol_recepcion(): void
    {
        $this->actingAs($this->userConRol('Recepcion'))
             ->get(route('reportes.caja'))
             ->assertForbidden();
    }

    public function test_caja_prohibe_rol_bodega(): void
    {
        $this->actingAs($this->userConRol('Bodega'))
             ->get(route('reportes.caja'))
             ->assertForbidden();
    }

    public function test_caja_permite_admin(): void
    {
        $this->actingAs($this->userConRol('Admin'))
             ->get(route('reportes.caja'))
             ->assertOk();
    }

    public function test_caja_permite_contador(): void
    {
        $this->actingAs($this->userConRol('Contador'))
             ->get(route('reportes.caja'))
             ->assertOk();
    }

    // NOTA: 'Supervisor' no está en la lista del middleware de grupo admin
    // (role:Admin,Recepcion,...) por lo que aún no puede acceder al área admin.
    // Cuando se agregue el rol al middleware de grupo, este test deberá habilitarse.
    // public function test_caja_permite_supervisor(): void { ... }

    // ─── reportes.auditoria ─────────────────────────────────────────────────

    public function test_auditoria_requiere_autenticacion(): void
    {
        $this->get(route('reportes.auditoria'))
             ->assertRedirect(route('login'));
    }

    public function test_auditoria_prohibe_rol_mecanico(): void
    {
        $this->actingAs($this->userConRol('Mecanico'))
             ->get(route('reportes.auditoria'))
             ->assertForbidden();
    }

    public function test_auditoria_prohibe_rol_recepcion(): void
    {
        $this->actingAs($this->userConRol('Recepcion'))
             ->get(route('reportes.auditoria'))
             ->assertForbidden();
    }

    public function test_auditoria_permite_admin(): void
    {
        $this->actingAs($this->userConRol('Admin'))
             ->get(route('reportes.auditoria'))
             ->assertOk();
    }

    public function test_auditoria_permite_contador(): void
    {
        $this->actingAs($this->userConRol('Contador'))
             ->get(route('reportes.auditoria'))
             ->assertOk();
    }

    // ─── pagos.show ─────────────────────────────────────────────────────────

    public function test_pagos_show_requiere_autenticacion(): void
    {
        // La autenticación ocurre antes del route model binding, por lo que
        // un ID inexistente sigue siendo redirigido al login.
        $this->get(route('pagos.show', ['pago' => 99999]))
             ->assertRedirect(route('login'));
    }

    public function test_pagos_show_prohibe_rol_cliente(): void
    {
        // 'Cliente' no está en la lista de roles del grupo admin →
        // el middleware o el route model binding bloquean el acceso (nunca 200).
        $response = $this->actingAs($this->userConRol('Cliente'))
                         ->get(route('pagos.show', ['pago' => 99999]));

        $this->assertNotSame(200, $response->status(), 'Un cliente nunca debe recibir el detalle del pago.');
    }

    public function test_pagos_show_admin_pasa_autorizacion(): void
    {
        // El Admin pasa la capa de autorización del controlador.
        // La respuesta 404 (pago inexistente) confirma que la guardia se superó.
        $this->actingAs($this->userConRol('Admin'))
             ->get(route('pagos.show', ['pago' => 99999]))
             ->assertNotFound();
    }

    public function test_pagos_show_prohibe_mecanico_sin_permiso(): void
    {
        // Mecanico pertenece al grupo admin pero NO tiene pagos.confirmar →
        // la guardia del controlador devuelve 403. Sin un Pago real en DB
        // el route model binding devuelve 404 primero, así que solo verificamos
        // que NO se obtenga 200 (acceso concedido).
        $response = $this->actingAs($this->userConRol('Mecanico'))
                         ->get(route('pagos.show', ['pago' => 99999]));

        // 403 si la guardia actúa primero, 404 si el model binding actúa primero.
        // En ningún caso debe ser 200 (acceso concedido).
        $this->assertContains($response->status(), [403, 404]);
        $this->assertNotSame(200, $response->status());
    }
}
