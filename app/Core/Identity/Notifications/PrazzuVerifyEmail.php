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
            ->subject('Confirme seu e-mail no Prazzu Tools')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Confirme seu endereço de e-mail para proteger sua conta e garantir a recuperação dos resultados salvos.')
            ->action('Confirmar meu e-mail', $verificationUrl)
            ->line('As ferramentas continuam completas e gratuitas mesmo sem confirmação. A verificação protege apenas os dados vinculados à sua conta.')
            ->line('Se você não criou esta conta, nenhuma ação é necessária.');
    }
}
