<?php
 namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $applicationId;

    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

public function broadcastOn(): array
{
    
    return [
        new Channel('applications'), // Remove 'Private'
    ];
}

    public function broadcastAs()
    {
        return 'application.updated';
    }
}