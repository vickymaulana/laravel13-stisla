<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * OTP Password Reset Notification
 *
 * This notification sends an OTP (One-Time Password) to the user's email
 * for password reset verification. It demonstrates how to create custom
 * email notifications in Laravel.
 */
class OtpPasswordResetNotification extends Notification
{
    /**
     * The OTP code to be sent to the user
     */
    private string $otp;

    private int $expireMinutes;

    /**
     * Create a new notification instance
     *
     * @param  string  $otp  The one-time password for password reset
     * @return void
     */
    public function __construct(string $otp, int $expireMinutes = 10)
    {
        $this->otp = $otp;
        $this->expireMinutes = $expireMinutes;
    }

    /**
     * Get the notification's delivery channels
     *
     * This method defines which channels the notification will be sent through.
     * Available channels: 'mail', 'database', 'broadcast', 'nexmo', 'slack'
     *
     * @param  mixed  $notifiable  The entity receiving the notification (usually User model)
     * @return array Array of notification channels
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification
     *
     * This method builds the email message that will be sent to the user.
     * Laravel's MailMessage provides a fluent interface for building emails.
     *
     * @param  mixed  $notifiable  The entity receiving the notification
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset OTP')
            ->line('Your OTP code is: '.$this->otp)
            ->line('This code expires in '.$this->expireMinutes.' minute(s).')
            ->line('Use this OTP together with your reset token link.')
            ->action('Reset Password', url('/password/reset'))
            ->line('If you did not request this, please ignore this email.');
    }
}
