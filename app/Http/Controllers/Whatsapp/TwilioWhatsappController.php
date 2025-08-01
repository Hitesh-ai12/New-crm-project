<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappMessage;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;




class TwilioWhatsappController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => 'required|string',
        ]);

        $user = auth()->user();

        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_WHATSAPP_NUMBER');

            $client = new Client($sid, $token);

            foreach ($request->recipients as $recipient) {
                try {
                    // Send WhatsApp message
                    $twilioResponse = $client->messages->create("whatsapp:$recipient", [
                        'from' => "whatsapp:$from",
                        'body' => $request->message,
                    ]);

                    // Optional: Lookup lead by phone
                    $lead = Lead::where('phone', $recipient)->first();

                    // Store message in DB
                    $whatsappMessage = WhatsappMessage::create([
                        'user_id'   => $user->id,
                        'lead_id'   => $lead?->id,
                        'phone'     => $recipient,
                        'message'   => $request->message,
                        'direction' => 'outgoing',
                        'status'    => 'sent',
                        'is_read'   => false,
                        'sent_at'   => now(),
                    ]);
                    
                    broadcast(new \App\Events\WhatsappMessageReceived($whatsappMessage))->toOthers();

                } catch (\Exception $e) {
                    // Optional: Store as failed
                    WhatsappMessage::create([
                        'user_id'   => $user->id,
                        'lead_id'   => $lead?->id ?? null,
                        'phone'     => $recipient,
                        'message'   => $request->message,
                        'direction' => 'outgoing',
                        'status'    => 'failed',
                        'is_read'   => false,
                        'sent_at'   => now(),
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'All messages processed.']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp message sending failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function incoming(Request $request)
    {
        try {
            $from = str_replace('whatsapp:', '', $request->input('From'));
            $body = $request->input('Body');

            // Find matching lead by phone
            $lead = Lead::where('phone', $from)->first();

            if (!$lead) {
                Log::warning("📛 No matching lead found for WhatsApp number: $from");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lead not found for number: ' . $from,
                ], 404);
            }

            // Save incoming message
            $incomingMessage = WhatsappMessage::create([
                'user_id'   => $lead->user_id,
                'lead_id'   => $lead->id,
                'phone'     => $from,
                'direction' => 'incoming',
                'message'   => $body,
                'status'    => 'delivered',
                'is_read'   => false,
                'sent_at'   => now(),
            ]);

            broadcast(new \App\Events\WhatsappMessageReceived($incomingMessage))->toOthers();

            Log::info("✅ Incoming WhatsApp message saved from: $from (lead_id: {$lead->id}, user_id: {$lead->user_id})");

            return response()->json(['status' => 'received']);
        } catch (\Exception $e) {
            Log::error("❌ Error receiving WhatsApp message: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function getLeadMessages($leadId)
    {
        $userId = auth()->id();

        $messages = WhatsappMessage::where('lead_id', $leadId)
            ->where('user_id', $userId)
            ->orderBy('sent_at', 'asc')
            ->get(['message', 'direction', 'sent_at']);

        return response()->json($messages->map(function ($msg) {
            return [
                'text' => $msg->message,
                'from' => $msg->direction === 'incoming' ? 'them' : 'me',
                'time' => \Carbon\Carbon::parse($msg->sent_at)->format('h:i A'),
            ];
        }));
    }

    public function getChatList()
    {

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = WhatsappMessage::with('lead')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('lead_id')
            ->map(function ($messages, $leadId) {
                $lead = $messages->first()->lead;

                // Skip if lead doesn't exist
                if (!$lead) {
                    return null;
                }

                return [
                    'id' => $leadId,
                    'name' => trim($lead->first_name . ' ' . $lead->last_name),
                    'phone' => $lead->phone,
                    'lastMessage' => $messages->last()->message,
                    'messages' => $messages->map(function ($msg) {
                        return [
                            'text' => $msg->message,
                            'from' => $msg->direction === 'outgoing' ? 'me' : 'them', 
                            'time' => $msg->created_at->format('h:i A'),
                        ];
                    })->toArray(),
                ];
            })
            ->filter() 
            ->values();

        return response()->json($messages);
    }

    public function getMessagesByLeadId($leadId)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = WhatsappMessage::where('lead_id', $leadId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'text' => $msg->message,
                    'from' => $msg->direction === 'outgoing' ? 'me' : 'them',
                    'time' => $msg->created_at->format('h:i A'),
                ];
            });

        return response()->json($messages);
    }

}
