<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SezrisNotification extends Notification
{
    use Queueable;

    public string $message; // store the custom message

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // or ['mail','database'] if you want
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message, // use the custom message
        ];
    }
}