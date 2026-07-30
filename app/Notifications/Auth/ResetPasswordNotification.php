<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecer contraseña — Taller Automotrices SC-BOL')
            ->greeting('¡Hola, ' . ($notifiable->persona?->nombre ?? $notifiable->email) . '!')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en **Taller Automotrices SC-BOL**.')
            ->line('Haz clic en el botón de abajo para crear una nueva contraseña.')
            ->action('🔑 Restablecer mi contraseña', $url)
            ->line('Este enlace expirará en **60 minutos**.')
            ->line('Si no solicitaste este cambio, no es necesario que hagas nada — tu contraseña no será modificada.')
            ->salutation('El equipo de Taller Automotrices SC-BOL');
    }
}
