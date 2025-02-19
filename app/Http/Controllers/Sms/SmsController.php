<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $data = $request->all();

        // Send SMS using Twilio
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $twilio->messages->create(
            $data['to'],
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $data['message']
            ]
        );

        return response()->json(['status' => 'SMS sent']);
    }
}
