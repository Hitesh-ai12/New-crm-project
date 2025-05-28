<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead; 
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
        public function sendSms(Request $request)
        {
            try {
                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

                $message = $twilio->messages->create(
                    $request->to,
                    [
                        'from' => env('TWILIO_PHONE_NUMBER'),
                        'body' => $request->message,
                    ]
                );

                return response()->json(['success' => true, 'message' => 'SMS sent successfully!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        private function sendSmsToLead($phone, $message)
        {
        
            Http::post('https://sms-provider.com/api/send', [
                'to' => $phone,
                'message' => $message,
                'api_key' => env('SMS_API_KEY'), 
            ]);
        }

        // Set the default signature
        public function setDefault(Request $request)
        {
            $signature = Signature::find($request->id);

            if ($signature) {
                Signature::where('is_default', true)->update(['is_default' => false]);

                $signature->update(['is_default' => true]);
            }

            return response()->json($signature);
        }

        // Delete a signature
        public function destroy($id)
        {
            $signature = Signature::find($id);
            if ($signature) {
                $signature->delete();
            }

            return response()->json(['message' => 'Signature deleted successfully.']);
        }
        
}

