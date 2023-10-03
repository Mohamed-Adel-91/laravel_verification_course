<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;

class Twilio
{
    public function send($merchant)
    {
        // Your Account SID and Auth Token from console.twilio.com
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $client = new Client($sid, $token);

        try {
            // Use the Client to make requests to the Twilio REST API
            $client->messages->create(
                // The number you'd like to send the message to
                $merchant->phone,
                [
                    // A Twilio phone number you purchased at https://console.twilio.com
                    'from' => env('TWILIO_FROM_NUMBER'),
                    // The body of the text message you'd like to send
                    'body' => "Hey $merchant->name! Your OTP is $merchant->otp!"
                ]
            );
        } catch (TwilioException $e) {
            Log::alert($e->getMessage());
        }
    }
}
