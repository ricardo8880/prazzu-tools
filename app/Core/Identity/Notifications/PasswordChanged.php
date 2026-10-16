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
            ->subject('Your account password was changed')
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('Your Prazzu Tools account password was changed successfully.')
            ->line('If you made this change, no further action is required.')
            ->action('Reset my password', route('password.request'))
            ->line('If you do not recognize this change, reset your password immediately.');
    }
}
