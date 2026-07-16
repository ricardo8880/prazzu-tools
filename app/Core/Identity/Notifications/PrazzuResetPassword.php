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
            ->subject('Redefina sua senha do Prazzu Tools')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta.')
            ->action('Criar nova senha', $url)
            ->line('Este link expira em '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutos.')
            ->line('Se você não solicitou a redefinição, ignore esta mensagem. Sua senha permanecerá inalterada.');
    }
}
