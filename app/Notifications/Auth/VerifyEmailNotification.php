<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Activa tu cuenta en Taller Automotrices SC-BOL')
            ->greeting('¡Hola, ' . $notifiable->persona?->nombre . '!')
            ->line('Gracias por registrarte en **Taller Automotrices SC-BOL**.')
            ->line('Haz clic en el botón de abajo para verificar tu correo electrónico y activar tu cuenta.')
            ->action('✅ Activar mi cuenta', $verifyUrl)
            ->line('Este enlace expirará en **60 minutos**.')
            ->line('Si no creaste una cuenta en Taller Automotrices SC-BOL, puedes ignorar este correo.')
            ->salutation('El equipo de Taller Automotrices SC-BOL');
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
