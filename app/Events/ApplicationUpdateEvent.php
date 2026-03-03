<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ApplicationUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $application;

    public function __construct($applicationData)
    {
        // Accept the data as a ready-to-go array
        $this->application = $applicationData;
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