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
        $repuesto = $this->consulta->repuesto;
        $total    = number_format(($repuesto?->precio_venta ?? 0) * $this->consulta->cantidad, 2);

        return (new MailMessage)
            ->subject('🎉 ¡Pago confirmado! Tu pedido está listo')
            ->greeting('¡Hola, ' . $this->consulta->nombre . '!')
            ->line('Tu pago fue **confirmado exitosamente** por nuestro equipo.')
            ->line('')
            ->line('**Producto:** ' . ($repuesto?->nombre ?? '—') . ($repuesto?->codigo ? " ({$repuesto->codigo})" : ''))
            ->line('**Cantidad:** ' . $this->consulta->cantidad . ' unidad(es)')
            ->line('**Total pagado: Bs ' . $total . '**')
            ->line('')
            ->line('Nuestro equipo se pondrá en contacto contigo para coordinar la entrega o el retiro del producto.')
            ->line($this->consulta->pago_notas ? 'Nota del equipo: ' . $this->consulta->pago_notas : '')
            ->salutation('Gracias por tu compra — Taller Automotrices SC-BOL');
    }
}
