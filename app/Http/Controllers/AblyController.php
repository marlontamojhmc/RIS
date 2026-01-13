<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ably\AblyRest;

class AblyController extends Controller
{
    public function publish(Request $request)
    {
        $request->validate([
            'channelName' => 'required|string',
            'message' => 'required|string',
        ]);

        $ably = new AblyRest(env('KEY'));

        $channel = $ably->channels->get($request->channelName);
        $channel->publish('first', $request->message);

        return response()->json(['status' => 'ok']);
    }

    // Optional: return client token request (for token auth)
    public function tokenRequest()
    {
        $ably = new AblyRest(env('VITE_ABLY_KEY'));
        $tokenRequest = $ably->auth->createTokenRequest(); // create token request for client
        return response()->json($tokenRequest);
    }
}
