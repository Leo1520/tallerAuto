<?php

namespace App\Notifications;

use App\Models\Repuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevaConsultaRepuestoNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Repuesto $repuesto,
        private readonly string   $clienteNombre,
        private readonly int      $cantidad
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nueva consulta de repuesto — {$this->repuesto->nombre}")
            ->greeting('Nueva solicitud de producto')
            ->line("**Cliente:** {$this->clienteNombre}")
            ->line("**Producto:** {$this->repuesto->nombre}" . ($this->repuesto->codigo ? " ({$this->repuesto->codigo})" : ''))
            ->line("**Cantidad solicitada:** {$this->cantidad}")
            ->line("**Precio unitario:** Bs " . number_format($this->repuesto->precio_venta, 2))
            ->line('Revisa el panel de administración para gestionar esta solicitud.')
            ->salutation('Taller Automotrices SC-BOL');
    }
}
