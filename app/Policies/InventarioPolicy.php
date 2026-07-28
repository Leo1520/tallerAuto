<?php

namespace App\Policies;

use App\Models\User;

class InventarioPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function verRepuestos(User $user): bool
    {
        return $user->hasPermission('inventario.ver');
    }

    public function gestionarRepuestos(User $user): bool
    {
        return $user->hasPermission('inventario.crear') || $user->hasPermission('inventario.editar');
    }

    public function registrarMovimiento(User $user): bool
    {
        return $user->hasPermission('inventario.movimientos');
    }
}
