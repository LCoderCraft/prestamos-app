<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Restablecimiento de Contrasena - Prestamos FIM')
            ->greeting('Hola ' . $notifiable->username . ',')
            ->line('Recibiste este correo porque solicitaste restablecer tu contrasena.')
            ->line('Tu codigo de verificacion es:')
            ->line('**' . $this->code . '**')
            ->line('Este codigo expirara en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este mensaje.')
            ->salutation('Sistema de Prestamos UAS');
    }
}
