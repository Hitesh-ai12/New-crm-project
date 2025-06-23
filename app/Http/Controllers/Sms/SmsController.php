<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\SmsMessage;
use App\Models\Lead;
use App\Models\IncomingSms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

            $leadIds = $request->lead_ids;
            $toNumbers = explode(',', $request->to);

            if (count($leadIds) !== count($toNumbers)) {
                return response()->json(['success' => false, 'error' => 'Lead IDs and phone numbers count mismatch.'], 400);
            }

            $messageTemplate = $request->message;

            foreach ($leadIds as $index => $leadId) {
                $to = trim($toNumbers[$index]);
                $lead = Lead::find($leadId);

                if (!$lead) {
                    continue;
                }

                $placeholders = [
                    '{{first_name}}' => $lead->first_name,
                    '{{last_name}}' => $lead->last_name ?? '',
                    '{{email}}' => $lead->email ?? '',
                    '{{phone}}' => $lead->phone ?? '',
                    '{{city}}' => $lead->city ?? '',
                ];

                $personalizedMessage = strtr($messageTemplate, $placeholders);

                // Send SMS via Twilio
                $twilio->messages->create($to, [
                    'from' => $twilioPhoneNumber,
                    'body' => $personalizedMessage,
                ]);

                // Save in `sms_messages` table
                SmsMessage::create([
                    'user_id'         => $request->user_id,
                    'lead_id'         => $leadId,
                    'from'            => $twilioPhoneNumber,
                    'to'              => $to,
                    'message'         => $personalizedMessage,
                    'type'            => 'sent',
                    'delivery_status' => 'sent', // change to 'delivered' if you handle webhook later
                    'timestamp'       => now(),
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
                Log::channel('twilio_sms')->info('Incoming SMS Webhook Hit', [
                    'From' => $request->input('From'),
                    'To' => $request->input('To'),
                    'Body' => $request->input('Body'),
                ]);

                $from = $request->input('From');
                $to = $request->input('To');
                $message = $request->input('Body');
                $receivedAt = now();

                $lead = Lead::where('phone', $from)->first();

                if (!$lead) {
                    Log::channel('twilio_sms')->warning("No matching lead found for number: $from");
                    return response('No matching lead found', 200);
                }

                SmsMessage::create([
                    'user_id' => $lead->user_id ?? null,
                    'lead_id' => $lead->id,
                    'from' => $from,
                    'to' => $to,
                    'message' => $message,
                    'type' => 'received',
                    'delivery_status' => 'delivered',
                    'timestamp' => $receivedAt,
                ]);

                Log::channel('twilio_sms')->info("Received SMS saved for Lead ID: " . $lead->id);

                return response('Message received and stored', 200);
            } catch (\Exception $e) {
                Log::channel('twilio_sms')->error('Error processing SMS', [
                    'error' => $e->getMessage()
                ]);
                return response('Error processing SMS', 500);
            }
        }

    public function getIncomingSms()
    {
        try {
            $userId = Auth::id();
            Log::info('Fetching incoming SMS for user ID: ' . $userId);

            $smsList = SmsMessage::where('user_id', $userId)
                ->where('type', 'received')
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function ($s) {
                    $dt = Carbon::parse($s->timestamp);
                    return [
                        'id' => 'sms-rec-' . $s->id,
                        'type' => 'sms',
                        'direction' => 'received',
                        'phone' => $s->from,
                        'title' => 'Received SMS',
                        'description' => $s->message,
                        'leadId' => $s->lead_id,
                        'date' => $dt->format('Y-m-d'),
                        'time' => $dt->format('H:i'),
                    ];
                });

            return response()->json($smsList);
        } catch (\Exception $e) {
            Log::error('Error fetching incoming SMS', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch SMS'], 500);
        }
    }

    public function getSmsByLead($leadId)
    {
        try {
            $userId = Auth::id();

            $smsList = SmsMessage::where('user_id', $userId)
                ->where('lead_id', $leadId)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function ($s) {
                    $dt = Carbon::parse($s->timestamp);

                    return [
                        'id' => 'sms-' . $s->type . '-' . $s->id,
                        'type' => 'sms',
                        'direction' => $s->type,
                        'phone' => $s->type === 'sent' ? $s->to : $s->from,
                        'title' => $s->type === 'sent' ? 'Sent SMS' : 'Received SMS',
                        'description' => $s->message,
                        'leadId' => $s->lead_id,
                        'date' => $dt->format('Y-m-d'),
                        'time' => $dt->format('H:i'),
                    ];
                });

            return response()->json($smsList);

        } catch (\Exception $e) {
            Log::error('Failed to fetch lead SMS', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch SMS'], 500);
        }
    }
public function getLeadList()
{
    try {
        $userId = Auth::id();

        $leads = SmsMessage::where('user_id', $userId)
            ->select('lead_id')
            ->distinct()
            ->with(['lead' => function ($q) {
                $q->select('id', 'first_name', 'last_name');
            }])
            ->get()
            ->map(function ($s) {
                $lead = $s->lead;
                return [
                    'leadId' => $s->lead_id,
                    'name' => $lead ? trim($lead->first_name . ' ' . $lead->last_name) : 'Unknown',
                ];
            });

        return response()->json($leads);

    } catch (\Exception $e) {
        Log::error('Failed to fetch lead list', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to fetch leads'], 500);
    }
}
public function getLeads()
{
    try {
        $userId = Auth::id();

        $leadIds = SmsMessage::where('user_id', $userId)
            ->whereNotNull('lead_id')
            ->pluck('lead_id')
            ->unique();

        $leads = DB::table('leads')
            ->whereIn('id', $leadIds)
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) AS name"))
            ->get();

        return response()->json($leads);

    } catch (\Exception $e) {
        \Log::error('Failed to fetch lead list', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Server Error'], 500);
    }
}
    // public function smsStatus(Request $request)
    // {
    //     $sid = $request->input('MessageSid');
    //     $status = $request->input('MessageStatus'); 

    //     $sms = SentSms::where('message_sid', $sid)->first();
    //     if ($sms) {
    //         $sms->status = $status;
    //         $sms->save();
    //     }

    //     return response()->json(['success' => true]);
    // }


    
    // public function getSentSms(Request $request)
    // {
    //     $sentSms = SentSms::where('user_id', Auth::id())
    //         ->orderBy('sent_at', 'desc')
    //         ->get();

    //     return response()->json($sentSms);
    // }


    // public function getLeadSms($id)
    // {
    //     try {
    //         $user = auth()->user(); 

    //         if (!$user) {
    //             return response()->json(['error' => 'Unauthorized'], 401);
    //         }

    //         $incoming = IncomingSms::where('lead_id', $id)
    //             ->where('user_id', $user->id)
    //             ->get()
    //             ->map(function ($sms) {
    //                 $sms->direction = 'received';
    //                 return $sms;
    //             });

    //         $sent = SentSms::where('lead_id', $id)
    //             ->where('user_id', $user->id)
    //             ->get()
    //             ->map(function ($sms) {
    //                 $sms->direction = 'sent';
    //                 return $sms;
    //             });

    //         $merged = $incoming->merge($sent)->sortByDesc('created_at')->values();

    //         return response()->json($merged);
    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching lead SMS: ' . $e->getMessage());
    //         return response()->json(['error' => 'Server error'], 500);
    //     }
    // }

}
