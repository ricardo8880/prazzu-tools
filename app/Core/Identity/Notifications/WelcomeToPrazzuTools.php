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
            ->subject('Sua conta Prazzu Tools foi criada')
            ->greeting('Bem-vindo, '.$notifiable->name.'!')
            ->line('Sua conta gratuita foi criada com sucesso.')
            ->line('Todas as ferramentas já estão disponíveis sem limite. Sua conta existe para manter históricos, resultados e preferências salvos.')
            ->action('Acessar minha conta', route('account.show'))
            ->line('No futuro, esta conta poderá ser vinculada à identidade única do ecossistema Prazzu.');
    }
}
