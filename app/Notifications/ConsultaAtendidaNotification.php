<?php

namespace App\Notifications;

use App\Models\ConsultaRepuesto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

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
        $pagoUrl  = url('/cliente/consultas/pagar/' . $this->consulta->token);
        $total    = number_format(($repuesto?->precio_venta ?? 0) * $this->consulta->cantidad, 2);

        $mail = (new MailMessage)
            ->subject('✅ Solicitud aceptada — procede con el pago')
            ->greeting('¡Hola, ' . $this->consulta->nombre . '!')
            ->line('Tu solicitud de compra fue **aceptada** por nuestro equipo.')
            ->line('')
            ->line('**Producto:** ' . ($repuesto?->nombre ?? '—') . ($repuesto?->codigo ? " ({$repuesto->codigo})" : ''))
            ->line('**Cantidad:** ' . $this->consulta->cantidad . ' unidad(es)')
            ->line('**Precio unitario:** Bs ' . number_format($repuesto?->precio_venta ?? 0, 2))
            ->line('**Total a pagar: Bs ' . $total . '**')
            ->line('')
            ->line('Realiza el pago escaneando el **código QR** adjunto con tu app bancaria e ingresa el monto exacto.')
            ->line('Referencia/concepto: **Solicitud #' . $this->consulta->id . '**')
            ->action('📤 Subir comprobante de pago', $pagoUrl)
            ->line('Después de pagar, haz clic en el botón de arriba para enviarnos tu comprobante y confirmar tu pedido.');

        // Adjuntar el QR si el admin subió uno
        $qrPath = $this->consulta->qr_path;
        if ($qrPath && Storage::disk('public')->exists($qrPath)) {
            $mail->attach(Storage::disk('public')->path($qrPath), [
                'as'   => 'qr_pago.png',
                'mime' => 'image/png',
            ]);
        } elseif (file_exists(public_path('images/qr_banco_ganadero.png'))) {
            $mail->attach(public_path('images/qr_banco_ganadero.png'), [
                'as'   => 'qr_pago.png',
                'mime' => 'image/png',
            ]);
        }

        $mail->salutation('Taller Automotrices SC-BOL');

        return $mail;
    }
}
