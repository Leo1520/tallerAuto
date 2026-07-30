<?php

namespace App\Policies;

use App\Models\Adjunto;
use App\Models\OrdenServicio;
use App\Models\User;

class AdjuntoPolicy
{
    /**
     * Administrador pasa todo sin más comprobaciones.
     */
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Ver / descargar un adjunto: requiere permiso de ver órdenes.
     */
    public function view(User $user, Adjunto $adjunto): bool
    {
        return $user->hasPermission('ordenes.ver');
    }

    /**
     * Subir adjunto a una orden: requiere permiso de editar órdenes.
     */
    public function create(User $user, OrdenServicio $orden): bool
    {
        return $user->hasPermission('ordenes.editar');
    }

    /**
     * Eliminar adjunto: quien lo subió o quien tiene permiso de editar.
     */
    public function delete(User $user, Adjunto $adjunto): bool
    {
        return $user->id === $adjunto->user_id
            || $user->hasPermission('ordenes.editar');
    }
}
