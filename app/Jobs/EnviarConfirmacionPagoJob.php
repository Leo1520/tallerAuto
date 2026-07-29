<?php

namespace App\Jobs;

use App\Models\Pago;
use App\Models\User;
use App\Notifications\PagoConfirmadoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarConfirmacionPagoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries    = 3;
    public int $backoff  = 60;

    public function __construct(public readonly int $pagoId) {}

    public function handle(): void
    {
        $pago = Pago::with([
            'orden.vehiculo.cliente',
            'orden.factura',
            'metodoPago',
        ])->find($this->pagoId);

        if (! $pago) {
            Log::warning("EnviarConfirmacionPagoJob: pago #{$this->pagoId} no encontrado.");
            return;
        }

        $personaId = $pago->orden?->vehiculo?->cliente?->persona_id;
        if (! $personaId) return;

        $user = User::where('persona_id', $personaId)->first();
        $user?->notify(new PagoConfirmadoNotification($pago));
    }
}
