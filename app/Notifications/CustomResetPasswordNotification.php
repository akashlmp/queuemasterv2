<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword
{
    use Queueable;

    public function toMail($notifiable)
    {
        $resetUrl = $this->createUrl($notifiable);

        return (new MailMessage())
            ->subject('Reset Your QueueMaster Password')
            ->greeting('Dear QueueMaster User,')
            ->line('We\'ve received a request to reset the password for your QueueMaster account.')
            ->line('Please use the link below to reset your password:')
            ->action('Reset Password', $resetUrl)
            ->line('If you didn\'t initiate this request, please ignore this email. For any concerns, reach out to our support team immediately.')
            ->line('Thank you,')
            ->salutation('The QueueMaster Team');
    }

    protected function createUrl($notifiable)
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
