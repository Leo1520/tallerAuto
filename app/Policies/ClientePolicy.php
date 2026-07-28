<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('clientes.ver');
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return $user->hasPermission('clientes.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('clientes.crear');
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $user->hasPermission('clientes.editar');
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return $user->hasPermission('clientes.eliminar');
    }
}
