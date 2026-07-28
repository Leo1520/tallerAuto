<?php

namespace App\Policies;

use App\Models\OrdenServicio;
use App\Models\User;

class OrdenServicioPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ordenes.ver');
    }

    public function view(User $user, OrdenServicio $orden): bool
    {
        if ($user->hasRole('Mecanico')) {
            return $orden->mecanico?->persona_id === $user->persona_id;
        }

        return $user->hasPermission('ordenes.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ordenes.crear');
    }

    public function update(User $user, OrdenServicio $orden): bool
    {
        return $user->hasPermission('ordenes.editar');
    }

    public function cambiarEstado(User $user, OrdenServicio $orden): bool
    {
        if ($user->hasRole('Mecanico')) {
            return $orden->mecanico?->persona_id === $user->persona_id;
        }

        return $user->hasPermission('ordenes.cambiar_estado');
    }

    public function delete(User $user, OrdenServicio $orden): bool
    {
        return $user->hasPermission('ordenes.eliminar');
    }
}
