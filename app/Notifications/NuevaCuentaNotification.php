<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevaCuentaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $nombre,
        public readonly string $email,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva cuenta registrada — Taller Automotrices SC-BOL')
            ->greeting('Nuevo usuario registrado')
            ->line("**{$this->nombre}** ({$this->email}) acaba de crear una cuenta.")
            ->line('El usuario espera que le asignes un rol para poder acceder al sistema.')
            ->action('Ir a Gestión de Usuarios', route('usuarios.index'))
            ->line('Este mensaje fue generado automáticamente por Taller Automotrices SC-BOL.');
    }

    public function toArray(object $notifiable): array
    {
        return ['nombre' => $this->nombre, 'email' => $this->email];
    }
}
