<?php

namespace App\Services;

use App\Actions\Facturas\GenerarFacturaAction;
use App\Jobs\EnviarConfirmacionPagoJob;
use App\Models\Auditoria;
use App\Models\MetodoPago;
use App\Models\MovimientoCaja;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\User;
use App\Notifications\PagoRechazadoNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PagoService
{
    public function __construct(private GenerarFacturaAction $generarFactura) {}

    // ─── QR: cliente sube comprobante ────────────────────────────────────

    public function registrarPagoEnRevision(
        OrdenServicio $orden,
        MetodoPago    $metodo,
        float         $monto,
        ?UploadedFile $comprobante = null
    ): Pago {
        return DB::transaction(function () use ($orden, $metodo, $monto, $comprobante) {
            $pago = Pago::create([
                'orden_id'       => $orden->id,
                'metodo_pago_id' => $metodo->id,
                'user_id'        => auth()->id(),
                'monto'          => $monto,
                'moneda'         => 'BOB',
                'estado'         => 'En revisión',
            ]);

            if ($comprobante) {
                $ruta = $comprobante->store("comprobantes/{$orden->id}", 'public');
                $pago->comprobante()->create([
                    'ruta'            => $ruta,
                    'nombre_original' => $comprobante->getClientOriginalName(),
                    'tipo_mime'       => $comprobante->getMimeType(),
                    'tamano'          => $comprobante->getSize(),
                ]);
            }

            return $pago;
        });
    }

    // ─── Cajero confirma pago (QR revisado) ──────────────────────────────

    public function confirmarPago(Pago $pago, User $cajero): void
    {
        DB::transaction(function () use ($pago, $cajero) {
            // Releer con bloqueo exclusivo para prevenir doble confirmación concurrente.
            $pago = Pago::lockForUpdate()->findOrFail($pago->id);

            if ($pago->estado === 'Confirmado') {
                throw new \RuntimeException('Este pago ya fue confirmado anteriormente.');
            }

            $pago->update([
                'estado'               => 'Confirmado',
                'fecha_confirmacion'   => now(),
                'user_id'              => $cajero->id,
                'confirmado_por_id'    => $cajero->id,
                'confirmado_ip'        => request()->ip(),
                'metodo_confirmacion'  => $this->resolverMetodoConfirmacion($pago),
            ]);

            $pago->loadMissing(['orden', 'metodoPago']);
            $this->efectosConfirmacion($pago, $cajero);
        });

        EnviarConfirmacionPagoJob::dispatch($pago->id);
    }

    // ─── Cajero rechaza pago ─────────────────────────────────────────────

    public function rechazarPago(Pago $pago, string $motivo, User $cajero): void
    {
        $pago->update([
            'estado'       => 'Rechazado',
            'observaciones' => $motivo,
        ]);

        $user = $this->resolverUsuarioCliente($pago->orden);
        $user?->notify(new PagoRechazadoNotification($pago, $motivo));
    }

    // ─── Cajero registra pago en efectivo ────────────────────────────────

    public function registrarEfectivo(
        OrdenServicio $orden,
        MetodoPago    $metodo,
        float         $monto,
        float         $montoRecibido,
        User          $cajero
    ): Pago {
        $pago = null;

        DB::transaction(function () use ($orden, $metodo, $monto, $montoRecibido, $cajero, &$pago) {
            $cambio = round($montoRecibido - $monto, 2);

            $pago = Pago::create([
                'orden_id'             => $orden->id,
                'metodo_pago_id'       => $metodo->id,
                'user_id'              => $cajero->id,
                'monto'                => $monto,
                'moneda'               => 'BOB',
                'estado'               => 'Confirmado',
                'fecha_confirmacion'   => now(),
                'confirmado_por_id'    => $cajero->id,
                'confirmado_ip'        => request()->ip(),
                'metodo_confirmacion'  => 'Efectivo',
                'observaciones'        => "Efectivo recibido: Bs " . number_format($montoRecibido, 2)
                                        . " | Cambio entregado: Bs " . number_format($cambio, 2),
            ]);

            $pago->loadMissing(['metodoPago']);
            $pago->setRelation('orden', $orden);

            $this->efectosConfirmacion($pago, $cajero);
        });

        EnviarConfirmacionPagoJob::dispatch($pago->id);
        return $pago;
    }

    // ─── Efectos post-confirmación ────────────────────────────────────────

    private function efectosConfirmacion(Pago $pago, User $cajero): void
    {
        // 1. Movimiento de caja
        MovimientoCaja::create([
            'pago_id'    => $pago->id,
            'user_id'    => $cajero->id,
            'tipo'       => 'Ingreso',
            'concepto'   => "Orden #{$pago->orden->numero} — {$pago->metodoPago->nombre}",
            'monto'      => $pago->monto,
            'referencia' => $pago->referencia,
        ]);

        // 2. ¿Orden completamente pagada?
        $orden = $pago->orden->fresh(['pagos', 'factura']);
        $totalConfirmado = $orden->pagos->where('estado', 'Confirmado')->sum('monto');

        if ($totalConfirmado >= (float) $orden->total) {
            if (! $orden->factura?->estaEmitida()) {
                try {
                    $this->generarFactura->execute($orden);
                } catch (\LogicException) {
                    // Factura ya emitida — ok
                }
            }
            if ($orden->estado !== 'Cancelado') {
                $orden->update(['estado' => 'Entregado']);
            }
        }

        // 3. Auditoría
        Auditoria::create([
            'user_id'        => $cajero->id,
            'tipo_operacion' => 'confirmar_pago',
            'tabla'          => 'pagos',
            'registro_id'    => $pago->id,
            'cambios'        => [
                'estado' => 'Confirmado',
                'monto'  => $pago->monto,
                'metodo' => $pago->metodoPago->nombre,
            ],
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // ─── Helper: canal de confirmación ───────────────────────────────────

    private function resolverMetodoConfirmacion(Pago $pago): string
    {
        $nombre = strtolower($pago->metodoPago?->nombre ?? '');
        if (str_contains($nombre, 'qr'))       return 'QR-cajero';
        if (str_contains($nombre, 'efectivo')) return 'Efectivo';
        if (str_contains($nombre, 'tarjeta'))  return 'Tarjeta';
        if (str_contains($nombre, 'transfer')) return 'Transferencia';
        return 'Manual';
    }

    // ─── Helper: usuario cliente de la orden ─────────────────────────────

    private function resolverUsuarioCliente(OrdenServicio $orden): ?User
    {
        $personaId = $orden->vehiculo?->cliente?->persona_id;
        if (! $personaId) return null;
        return User::where('persona_id', $personaId)->first();
    }
}
