<?php

namespace App\Notifications;

use App\Models\ConsultaRepuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultaPagoRechazadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ConsultaRepuesto $consulta) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $repuesto = $this->consulta->repuesto;
        $pagoUrl  = url('/cliente/consultas/pagar/' . $this->consulta->token);

        return (new MailMessage)
            ->subject('⚠️ Comprobante rechazado — vuelve a intentarlo')
            ->greeting('Hola, ' . $this->consulta->nombre . '.')
            ->line('Lamentablemente tu comprobante de pago **no pudo ser verificado**.')
            ->line('')
            ->line('**Producto:** ' . ($repuesto?->nombre ?? '—'))
            ->line('**Motivo:** ' . ($this->consulta->pago_notas ?? 'El comprobante no coincide con el monto o no es legible.'))
            ->line('')
            ->line('Por favor vuelve a realizar el pago con el monto exacto y sube el comprobante nuevamente.')
            ->action('Subir comprobante nuevamente', $pagoUrl)
            ->salutation('Taller Automotrices SC-BOL');
    }
}
