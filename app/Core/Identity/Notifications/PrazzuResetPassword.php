<?php

namespace App\Core\Identity\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

final class PrazzuResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Prazzu Tools password')
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('We received a request to reset your account password.')
            ->action('Create new password', $url)
            ->line('Este link expira em '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutos.')
            ->line('If you did not request a password reset, ignore this message. Your password will remain unchanged.');
    }
}
