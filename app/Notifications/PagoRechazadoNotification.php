<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoRechazadoNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Pago   $pago,
        private readonly string $motivo
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orden = $this->pago->orden;

        return (new MailMessage)
            ->subject("Comprobante de pago rechazado — Orden #{$orden?->numero}")
            ->greeting("Hola {$notifiable->persona?->nombre},")
            ->line("El comprobante de pago enviado para la orden **#{$orden?->numero}** no pudo ser validado.")
            ->line("**Motivo:** {$this->motivo}")
            ->line('Por favor comunícate con nosotros o acércate a la sucursal para regularizar el pago.')
            ->salutation('El equipo de Taller Pro');
    }
}
