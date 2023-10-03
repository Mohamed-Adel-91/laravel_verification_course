<?php

namespace App\Services;

use Exception;
use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Illuminate\Support\Facades\Log;
use Vonage\Client\Credentials\Basic;


class Vonage
{
    public function send($merchant)
    {
        // Your Account SID and Auth Token from console.twilio.com
        $basic  = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $client = new Client($basic);

        try {
            $response = $client->sms()->send(
                new SMS($merchant->phone, env('APP_NAME'), "Your OTP is $merchant->otp")
            );
        } catch (Exception $e) {
            Log::alert($e->getMessage());
        }
    }
}
