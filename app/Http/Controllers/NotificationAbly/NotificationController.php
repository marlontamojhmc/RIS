<?php

namespace App\Http\Controllers\NotificationAbly;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ably\AblyRest;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $ablyKey = env('ABLY_KEY');
        if (!$ablyKey) {
            return response()->json(['error' => 'Ably key not set'], 500);
        }

        $ably = new AblyRest($ablyKey);

        $channel = $ably->channel('notifications');
        $channel->publish('new_notification', [
            'title' => $request->title ?? 'Test Notification',
            'message' => $request->message ?? 'Hello from Laravel!',
        ]);

        return response()->json(['success' => true]);
    }
}
