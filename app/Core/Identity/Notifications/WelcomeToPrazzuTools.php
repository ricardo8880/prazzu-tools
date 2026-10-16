<?php

namespace App\Core\Identity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WelcomeToPrazzuTools extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Prazzu Tools account has been created')
            ->greeting('Welcome, '.$notifiable->name.'!')
            ->line('Your free account has been created successfully.')
            ->line('All tools are already available without usage limits. Your account keeps your history, results, and preferences saved.')
            ->action('Open my account', route('account.show'))
            ->line('In the future, this account may be linked to the unified identity of the Prazzu ecosystem.');
    }
}
