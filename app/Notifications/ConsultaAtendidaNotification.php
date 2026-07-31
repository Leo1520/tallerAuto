<?php

namespace App\Notifications;

use App\Models\ConsultaRepuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultaAtendidaNotification extends Notification
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
        $cantidad = $this->consulta->cantidad;

        return (new MailMessage)
            ->subject('Tu solicitud de repuesto fue atendida — ' . ($repuesto?->nombre ?? 'Repuesto'))
            ->greeting('Hola, ' . $this->consulta->nombre . '!')
            ->line('Tu solicitud de compra ha sido **atendida** por nuestro equipo.')
            ->line('**Producto:** ' . ($repuesto?->nombre ?? '—') . ($repuesto?->codigo ? " ({$repuesto->codigo})" : ''))
            ->line('**Cantidad solicitada:** ' . $cantidad . ' unidad(es)')
            ->line('**Precio unitario:** Bs ' . number_format($repuesto?->precio_venta ?? 0, 2))
            ->line('Nos pondremos en contacto contigo pronto para coordinar la entrega o retiro del producto.')
            ->line('Si tienes alguna duda, comunícate con nosotros.')
            ->salutation('Taller Automotrices SC-BOL');
    }
}
