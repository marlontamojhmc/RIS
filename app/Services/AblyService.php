<?php

namespace App\Services;

use Ably\AblyRest;

class AblyService
{
    protected $ably;

    public function __construct()
    {
        $this->ably = new AblyRest(env('ABLY_API_KEY'));
    }

    public function publish($channelName, $event, $message)
    {
        $channel = $this->ably->channel($channelName);
        return $channel->publish($event, $message);
    }
}
