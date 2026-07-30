<?php

namespace App\Notifications;

use App\Models\OrdenServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenAsignadaNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly OrdenServicio $orden) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orden   = $this->orden;
        $placa   = $orden->vehiculo->placa;
        $cliente = $orden->vehiculo->cliente->persona->nombre;

        return (new MailMessage)
            ->subject("Nueva orden asignada {$orden->numero} — Taller Automotrices SC-BOL")
            ->greeting("Hola {$notifiable->nombre},")
            ->line("Se te ha asignado la orden **{$orden->numero}**.")
            ->line("**Vehículo:** {$placa}")
            ->line("**Cliente:** {$cliente}")
            ->line("**Prioridad:** {$orden->prioridad}")
            ->when($orden->fecha_entrega_estimada, fn($m) =>
                $m->line("**Entrega estimada:** " . $orden->fecha_entrega_estimada->format('d/m/Y H:i'))
            )
            ->action('Ver orden en el sistema', route('ordenes.show', $orden))
            ->line('Taller Automotrices SC-BOL — Sistema de Gestión Automotriz');
    }

    public function toArray(object $notifiable): array
    {
        return ['orden_id' => $this->orden->id];
    }
}
