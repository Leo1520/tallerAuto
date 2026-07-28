<?php

namespace App\Actions\Ordenes;

use App\Models\OrdenServicio;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;

class CrearOrdenAction
{
    public function __construct(private GenerarNumeroOrden $generador) {}

    public function execute(array $datos, array $servicios): OrdenServicio
    {
        return DB::transaction(function () use ($datos, $servicios) {
            $orden = OrdenServicio::create([
                'numero'                 => $this->generador->execute(),
                'vehiculo_id'            => $datos['vehiculo_id'],
                'sucursal_id'            => $datos['sucursal_id'] ?? null,
                'mecanico_id'            => $datos['mecanico_id'] ?? null,
                'fecha_entrega_estimada' => $datos['fecha_entrega_estimada'] ?? null,
                'estado'                 => 'Recibido',
                'prioridad'              => $datos['prioridad'] ?? 'Media',
                'descuento'              => $datos['descuento'] ?? 0,
                'observaciones'          => $datos['observaciones'] ?? null,
            ]);

            $subtotal = 0;

            foreach ($servicios as $item) {
                $servicio = Servicio::findOrFail($item['servicio_id']);
                $cantidad  = (int) ($item['cantidad'] ?? 1);
                $precio    = (float) ($item['precio_unitario'] ?? $servicio->precio);
                $sub       = round($cantidad * $precio, 2);
                $subtotal += $sub;

                $orden->detalles()->create([
                    'servicio_id'     => $servicio->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal'        => $sub,
                    'estado'          => 'Pendiente',
                ]);
            }

            $descuento  = (float) ($datos['descuento'] ?? 0);
            $impuestos  = round(($subtotal - $descuento) * 0.13, 2); // IVA 13%
            $total      = round($subtotal - $descuento + $impuestos, 2);

            $orden->update(compact('subtotal', 'impuestos', 'total'));

            return $orden;
        });
    }
}
