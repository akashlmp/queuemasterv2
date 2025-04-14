<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilioClient;

    public function __construct()
    {
        $this->twilioClient = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendBulkSMS($recipients, $message)
    {
        foreach ($recipients as $recipient) 
        {
            $this->twilioClient->messages->create(
                $recipient,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $message
                ]
            );
        }
    }
}
