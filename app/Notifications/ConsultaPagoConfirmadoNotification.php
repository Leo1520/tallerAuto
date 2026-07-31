<?php

namespace App\Notifications;

use App\Models\ConsultaRepuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultaPagoConfirmadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ConsultaRepuesto $consulta) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $consulta = $this->consulta;
        $repuesto = $consulta->repuesto;

        return (new MailMessage)
            ->subject('✅ Recibo de compra — Taller Automotrices SC-BOL')
            ->view('mail.recibo_compra', compact('consulta', 'repuesto'));
    }
}
