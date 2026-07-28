<?php

namespace App\Actions\Ordenes;

use App\Models\OrdenServicio;

class GenerarNumeroOrden
{
    public function execute(): string
    {
        $prefijo = 'ORD-' . now()->format('Ymd') . '-';

        $ultimo = OrdenServicio::where('numero', 'like', $prefijo . '%')
            ->orderByDesc('numero')
            ->value('numero');

        $secuencia = $ultimo ? (int) substr($ultimo, -4) + 1 : 1;

        return $prefijo . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
    }
}
