<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SezrisNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database']; // or ['mail','database']
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'This is a Sezris test notification!',
        ];
    }
}
