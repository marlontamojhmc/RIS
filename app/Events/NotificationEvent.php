<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;


class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    public function __construct($message)

    {
       $this->message = $message;    
         
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('app-notifications'), // public channel for simplicity
        ];
    }

    public function broadcastWith(): array
    {
        return [
           'message' => $this->message,
        ];
    }
}