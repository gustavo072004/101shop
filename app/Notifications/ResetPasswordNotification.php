<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutos = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Restablecer contraseña - 101 Shop')
            ->greeting('Hola, '.$notifiable->nombres.'.')
            ->line('Recibimos una solicitud para restablecer la contraseña de su cuenta de 101 Shop.')
            ->action('Restablecer contraseña', $url)
            ->line("Este enlace vencerá en {$minutos} minutos.")
            ->line('Si usted no solicitó este cambio, puede ignorar este correo.');
    }
}
