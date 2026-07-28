<?php

namespace App\Actions\Facturas;

use App\Models\Factura;
use App\Models\OrdenServicio;
use Illuminate\Support\Facades\DB;

class GenerarFacturaAction
{
    public function execute(OrdenServicio $orden, ?string $observaciones = null): Factura
    {
        // Seguridad: no emitir si ya existe factura emitida
        if ($orden->factura?->estaEmitida()) {
            throw new \LogicException('Esta orden ya tiene una factura emitida.');
        }

        return DB::transaction(function () use ($orden, $observaciones) {
            $numero = $this->generarNumero();

            // Upsert: si ya hay borrador lo actualiza, sino crea
            $factura = Factura::updateOrCreate(
                ['orden_id' => $orden->id],
                [
                    'numero'        => $numero,
                    'fecha_emision' => now(),
                    'subtotal'      => $orden->subtotal,
                    'iva'           => $orden->impuestos,
                    'total'         => $orden->total,
                    'estado'        => 'Emitida',
                    'observaciones' => $observaciones,
                ]
            );

            return $factura;
        });
    }

    private function generarNumero(): string
    {
        $hoy    = now()->format('Ymd');
        $prefijo = "FAC-{$hoy}-";

        $ultimo = Factura::where('numero', 'like', "{$prefijo}%")
            ->orderByDesc('numero')
            ->value('numero');

        $secuencia = $ultimo
            ? (int) substr($ultimo, -4) + 1
            : 1;

        return $prefijo . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
    }
}
