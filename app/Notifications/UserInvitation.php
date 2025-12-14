<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class UserInvitation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Generate password reset token that will be used for invitation acceptance
        $token = Password::createToken($notifiable);
        $url = route('invitation.show', ['token' => $token, 'email' => $notifiable->email]);

        return (new MailMessage)
                    ->subject('You\'ve been invited to join ' . config('app.name'))
                    ->line('You have been invited to create an account. Please click the button below to set your password and complete your registration.')
                    ->action('Accept Invitation', $url)
                    ->line('This invitation link will expire in 60 minutes.')
                    ->line('If you did not expect this invitation, you can safely ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
