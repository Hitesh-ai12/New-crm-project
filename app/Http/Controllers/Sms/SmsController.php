<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\SentSms;
use App\Models\Lead;
use App\Models\IncomingSms;

use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        try {
            $twilioSid = config('services.twilio.sid');
            $twilioAuthToken = config('services.twilio.token');
            $twilioPhoneNumber = config('services.twilio.phone_number');

            if (!$twilioSid || !$twilioAuthToken || !$twilioPhoneNumber) {
                return response()->json(['success' => false, 'error' => 'Twilio credentials are missing!'], 500);
            }

            $twilio = new Client($twilioSid, $twilioAuthToken);

            // Ensure both arrays are aligned
            $leadIds = $request->lead_ids;
            $toNumbers = explode(',', $request->to);

            if (count($leadIds) !== count($toNumbers)) {
                return response()->json(['success' => false, 'error' => 'Lead IDs and phone numbers count mismatch.'], 400);
            }

            foreach ($leadIds as $index => $leadId) {
                $to = trim($toNumbers[$index]);

                // Send SMS via Twilio
                $twilio->messages->create(
                    $to,
                    [
                        'from' => $twilioPhoneNumber,
                        'body' => $request->message,
                    ]
                );

                // Store each SMS
                SentSms::create([
                    'user_id' => $request->user_id, 
                    'lead_id' => $leadId,       
                    'from' => $twilioPhoneNumber,
                    'to' => $to,
                    'message' => $request->message,
                    'sent_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'All SMS messages sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function incomingSms(Request $request)
    {
        try {
            $from = $request->input('From');
            $to = $request->input('To');
            $message = $request->input('Body');
            $receivedAt = now();

            $lead = Lead::where('phone', $from)->first();

            if (!$lead) {
                return response('No matching lead found', 200); 
            }

            IncomingSms::create([
                'user_id' => $lead->user_id ?? null,
                'lead_id' => $lead->id,
                'from' => $from,
                'to' => $to,
                'message' => $message,
                'received_at' => $receivedAt,
            ]);

            return response('Message received and stored', 200);
        } catch (\Exception $e) {
            return response('Error processing SMS', 500);
        }
    }

    public function smsStatus(Request $request)
    {
        $sid = $request->input('MessageSid');
        $status = $request->input('MessageStatus'); 

        $sms = SentSms::where('message_sid', $sid)->first();
        if ($sms) {
            $sms->status = $status;
            $sms->save();
        }

        return response()->json(['success' => true]);
    }

    public function getSentSms(Request $request)
    {
        $sentSms = SentSms::where('user_id', Auth::id())
            ->orderBy('sent_at', 'desc')
            ->get();

        return response()->json($sentSms);
    }
    
    public function getIncomingSms()
    {
        $incoming = IncomingSms::where('user_id', Auth::id())
            ->orderBy('received_at', 'desc')
            ->get();

        return response()->json($incoming);
    }

    public function getLeadSms($id)
    {
        try {
            $user = auth()->user(); // ⛔ may be null if auth fails

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $incoming = IncomingSms::where('lead_id', $id)
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($sms) {
                    $sms->direction = 'received';
                    return $sms;
                });

            $sent = SentSms::where('lead_id', $id)
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($sms) {
                    $sms->direction = 'sent';
                    return $sms;
                });

            $merged = $incoming->merge($sent)->sortByDesc('created_at')->values();

            return response()->json($merged);
        } catch (\Exception $e) {
            \Log::error('Error fetching lead SMS: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

}
