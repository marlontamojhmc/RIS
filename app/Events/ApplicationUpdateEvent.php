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

    public $application;

public function __construct($application)
{
    // It's often safer to convert to array for broadcasting 
    // to avoid Serialization issues with complex Eloquent models
    $this->application = $application; 
}

    public function broadcastOn()
    {
        return new Channel('applications');
    }

    public function broadcastAs()
    {
        return 'ApplicationUpdated';
    }
}