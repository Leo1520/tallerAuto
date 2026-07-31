<?php

namespace App\Notifications;

use App\Models\ConsultaRepuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultaComprobanteNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ConsultaRepuesto $consulta) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $repuesto  = $this->consulta->repuesto;
        $adminUrl  = url('/admin/consultas/' . $this->consulta->id);

        return (new MailMessage)
            ->subject('🧾 Comprobante recibido — Solicitud #' . $this->consulta->id)
            ->greeting('Nuevo comprobante de pago')
            ->line('**Cliente:** ' . $this->consulta->nombre)
            ->line('**Producto:** ' . ($repuesto?->nombre ?? '—') . ($repuesto?->codigo ? " ({$repuesto->codigo})" : ''))
            ->line('**Cantidad:** ' . $this->consulta->cantidad . ' unidad(es)')
            ->line('El cliente subió un comprobante de pago. Revísalo en el panel de administración.')
            ->action('Revisar comprobante', $adminUrl)
            ->salutation('Taller Automotrices SC-BOL');
    }
}
