<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        try {
            // ✅ Get Twilio credentials from config
            $twilioSid = config('services.twilio.sid');
            $twilioAuthToken = config('services.twilio.token');
            $twilioPhoneNumber = config('services.twilio.phone_number');

            // ✅ Debugging: Check if credentials are loading properly
            if (!$twilioSid || !$twilioAuthToken || !$twilioPhoneNumber) {
                return response()->json(['success' => false, 'error' => 'Twilio credentials are missing!'], 500);
            }

            // ✅ Initialize Twilio Client
            $twilio = new Client($twilioSid, $twilioAuthToken);

            // ✅ Send SMS
            $message = $twilio->messages->create(
                $request->to,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $request->message,
                ]
            );

            return response()->json(['success' => true, 'message' => 'SMS sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
