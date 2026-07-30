<?php

namespace App\Notifications;

use App\Models\OrdenServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenEstadoNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly OrdenServicio $orden) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $estado  = $this->orden->estado;
        $numero  = $this->orden->numero;
        $placa   = $this->orden->vehiculo->placa;
        $cliente = $this->orden->vehiculo->cliente->persona->nombre;

        $msg = (new MailMessage)
            ->subject("Orden {$numero} — {$estado} | Taller Automotrices SC-BOL")
            ->greeting("Hola {$cliente},");

        if ($estado === 'Listo') {
            $msg->line("Tu vehículo **{$placa}** está listo para ser retirado.")
                ->line('Puedes pasar a recogerlo en el horario de atención del taller.')
                ->line("**Orden:** {$numero}")
                ->line("**Total a pagar:** Bs " . number_format($this->orden->total, 2));
        } elseif ($estado === 'Entregado') {
            $msg->line("Tu vehículo **{$placa}** ha sido entregado exitosamente.")
                ->line("**Orden:** {$numero}")
                ->line('Gracias por confiar en Taller Automotrices SC-BOL. ¡Esperamos verte pronto!');
        } else {
            $msg->line("El estado de tu orden **{$numero}** ha cambiado a: **{$estado}**.")
                ->line("Vehículo: **{$placa}**");
        }

        return $msg->line('Taller Automotrices SC-BOL — Sistema de Gestión Automotriz');
    }

    public function toArray(object $notifiable): array
    {
        return ['orden_id' => $this->orden->id, 'estado' => $this->orden->estado];
    }
}
