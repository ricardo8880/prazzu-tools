<?php

namespace App\Core\Identity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A senha da sua conta foi alterada')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('A senha da sua conta Prazzu Tools foi alterada com sucesso.')
            ->line('Se você realizou esta alteração, nenhuma outra ação é necessária.')
            ->action('Redefinir minha senha', route('password.request'))
            ->line('Se não reconhece a alteração, redefina a senha imediatamente.');
    }
}
