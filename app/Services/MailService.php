<?php
namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GmailService
{
    protected $client;

    public function __construct($token)
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->setAccessToken($token);
    }

    public function sendEmail($to, $subject, $body)
    {
        $gmail = new Gmail($this->client);

        $strRawMessage = "From: me\r\n";
        $strRawMessage .= "To: <$to>\r\n";
        $strRawMessage .= "Subject: $subject\r\n";
        $strRawMessage .= "MIME-Version: 1.0\r\n";
        $strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $strRawMessage .= $body;

        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');

        $message = new Message();
        $message->setRaw($mime);

        return $gmail->users_messages->send('me', $message);
    }
}
