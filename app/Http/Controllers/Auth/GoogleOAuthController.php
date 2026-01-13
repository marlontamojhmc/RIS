<?php

namespace App\Http\Controllers\Auth;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class GoogleOAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope('https://www.googleapis.com/auth/gmail.send');

        return redirect()->away($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        // Optional: Check for errors
        if (isset($token['error'])) {
            return redirect('/')->withErrors('Google auth failed: ' . $token['error_description']);
        }

        // Save token in session or DB
        Session::put('google_token', $token);

        return redirect('/send-gmail');
    }
}
