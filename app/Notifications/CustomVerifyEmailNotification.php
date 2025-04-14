<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // Generate the verification URL
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage())
            ->subject('Welcome to QueueMaster - Verify Your Account')
            ->greeting('Dear QueueMaster User,')
            ->line('Welcome aboard! We\'re thrilled to have you join QueueMaster, where efficiency meets simplicity.')
            ->line('To get started, we need to verify your account. Please click the button below to confirm your email address:')
            ->action('Verify Your Email Address', $verificationUrl) // Use the verification URL as the action URL
            ->line('If you have any questions or encounter any issues during the verification process, don\'t hesitate to reach out to our support team at [support email - TBC].')
            ->line('We\'re excited to embark on this journey with you!')
            ->line('Best regards,')
            ->salutation('The QueueMaster Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
