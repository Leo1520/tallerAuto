<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;

class VehiculoPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('vehiculos.ver');
    }

    public function view(User $user, Vehiculo $vehiculo): bool
    {
        return $user->hasPermission('vehiculos.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('vehiculos.crear');
    }

    public function update(User $user, Vehiculo $vehiculo): bool
    {
        return $user->hasPermission('vehiculos.editar');
    }

    public function delete(User $user, Vehiculo $vehiculo): bool
    {
        return $user->hasPermission('vehiculos.eliminar');
    }
}
