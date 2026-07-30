<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoConfirmadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Pago $pago) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orden   = $this->pago->orden;
        $factura = $orden?->factura;

        $mail = (new MailMessage)
            ->subject("Pago confirmado — Orden #{$orden?->numero}")
            ->greeting("Hola {$notifiable->persona?->nombre},")
            ->line("Tu pago de **Bs " . number_format($this->pago->monto, 2) . "** ha sido confirmado.")
            ->line("**Orden:** #{$orden?->numero}")
            ->line("**Método de pago:** {$this->pago->metodoPago?->nombre}")
            ->line("**Fecha de confirmación:** " . $this->pago->fecha_confirmacion?->format('d/m/Y H:i'));

        if ($factura?->estaEmitida()) {
            $mail->line("Tu factura **{$factura->numero}** ha sido emitida.");
        }

        $mail->line('Gracias por confiar en Taller Automotrices SC-BOL.')
             ->salutation('El equipo de Taller Automotrices SC-BOL');

        return $mail;
    }
}
