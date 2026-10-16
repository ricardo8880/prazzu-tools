<?php

namespace App\Core\Identity\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

final class PrazzuVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email on Prazzu Tools')
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('Verify your email address to protect your account and ensure access to your saved results.')
            ->action('Verify my email', $verificationUrl)
            ->line('The tools remain complete and free even without verification. Verification only protects the data linked to your account.')
            ->line('If you did not create this account, no action is required.');
    }
}
