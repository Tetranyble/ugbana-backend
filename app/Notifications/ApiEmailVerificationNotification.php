<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ApiEmailVerificationNotification extends Notification
{
    use Queueable;

    public User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get("Hi {$notifiable->fullname}, thanks for Joining. This is your verification code: {$notifiable->verification_code}"))
            //->action(Lang::get('Verify Email Address'), "hello route")
            ->line(Lang::get('If you did not create an account, no further action is required.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'header' => 'Verify Email Address',
            'content' => "Hi {$notifiable->fullname}, thanks for Joining. This is your verification code: {$notifiable->verification_code}",
        ];
    }
}
