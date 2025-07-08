<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappMessage;
use App\Models\Lead;



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
                    WhatsappMessage::create([
                        'user_id'   => $user->id,
                        'lead_id'   => $lead?->id,
                        'phone'     => $recipient,
                        'message'   => $request->message,
                        'direction' => 'outgoing',
                        'status'    => 'sent',
                        'is_read'   => false,
                        'sent_at'   => now(),
                    ]);

                    Log::info("✅ WhatsApp message sent to: $recipient");
                } catch (\Exception $e) {
                    Log::error("❌ Failed to send WhatsApp message to $recipient: " . $e->getMessage());

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
            Log::error("🚨 Unexpected WhatsApp error: " . $e->getMessage());
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
            WhatsappMessage::create([
                'user_id'   => $lead->user_id,
                'lead_id'   => $lead->id,
                'phone'     => $from,
                'direction' => 'incoming',
                'message'   => $body,
                'status'    => 'delivered',
                'is_read'   => false,
                'sent_at'   => now(),
            ]);

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

}
