<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Google_Service_Gmail_WatchRequest;
use App\Models\Lead;
use App\Models\EmailReply;
use Carbon\Carbon;

class GmailWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->input('message.data');
        $decoded = json_decode(base64_decode($data), true);

        Log::info('📬 Pub/Sub Data:', $decoded);
        $historyId = $decoded['historyId'] ?? null;

        Log::info("🔁 Gmail history ID: $historyId");

        // Load access token
        $accessToken = json_decode(Storage::get('gmail/token.json'), true);

        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/gmail/credentials.json'));
        $client->addScope([
            Google_Service_Gmail::GMAIL_READONLY,
            Google_Service_Gmail::GMAIL_MODIFY
        ]);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            Log::error('Token expired');
            return response()->json(['error' => 'Token expired'], 401);
        }

        $gmail = new \Google_Service_Gmail($client);

        $messages = $gmail->users_messages->listUsersMessages('me', [
            'labelIds' => ['INBOX'],
            'maxResults' => 1
        ]);

        if (count($messages->getMessages()) === 0) {
            Log::info('No new email found.');
            return response()->json(['message' => 'No new email found.']);
        }

        $msgId = $messages->getMessages()[0]->getId();
        $message = $gmail->users_messages->get('me', $msgId, ['format' => 'full']);
        $payload = $message->getPayload();
        $headers = collect($payload->getHeaders());

        $from = optional($headers->firstWhere('name', 'From'))->value;
        $to = optional($headers->firstWhere('name', 'To'))->value;
        $subject = optional($headers->firstWhere('name', 'Subject'))->value;
        $receivedAt = Carbon::createFromTimestamp($message->getInternalDate() / 1000);

        // Decode message body (may be in parts)
        $body = '';
        if ($payload->getBody()->getSize() > 0) {
            $body = base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
        } elseif ($parts = $payload->getParts()) {
            foreach ($parts as $part) {
                if ($part['mimeType'] === 'text/plain') {
                    $body = base64_decode(strtr($part['body']['data'], '-_', '+/'));
                    break;
                }
            }
        }

        // Search lead by email (you may adjust this logic)
        $lead = Lead::where('email', $from)->first();

        // Store in DB
        EmailReply::create([
            'user_id' => $lead->user_id ?? null,
            'lead_id' => $lead->id ?? null,
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $body,
            'received_at' => $receivedAt,
        ]);

        return response()->json(['status' => 'saved']);
    }

    public function redirectToGoogle()
    {
        $client = new \Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $client->addScope([
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.modify'
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        if ($request->has('code')) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($request->get('code'));

            if (isset($accessToken['error'])) {
                return response()->json(['error' => $accessToken['error']], 500);
            }

            // Preserve refresh_token if already stored
            $tokenPath = storage_path('app/gmail/token.json');
            if (Storage::exists('gmail/token.json')) {
                $existingToken = json_decode(Storage::get('gmail/token.json'), true);
                if (!isset($accessToken['refresh_token']) && isset($existingToken['refresh_token'])) {
                    $accessToken['refresh_token'] = $existingToken['refresh_token'];
                }
            }

            Storage::put('gmail/token.json', json_encode($accessToken));

            return response()->json(['message' => '✅ Gmail token saved successfully!']);
        }

        return response()->json(['error' => 'No code returned'], 400);
    }


   public function fetchLatestEmail()
    {
        if (!Storage::exists('gmail/token.json')) {
            return response()->json(['error' => '❌ Token file not found.'], 404);
        }

        $accessToken = json_decode(Storage::get('gmail/token.json'), true);

        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/gmail/credentials.json'));
        $client->addScope(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            return response()->json(['error' => 'Access token expired, refresh token required.'], 401);
        }

        $gmail = new \Google_Service_Gmail($client);

        $messages = $gmail->users_messages->listUsersMessages('me', [
            'labelIds' => ['INBOX'],
            'maxResults' => 1,
        ]);

        if (count($messages->getMessages()) === 0) {
            return response()->json(['message' => 'No new emails.']);
        }

        $msgId = $messages->getMessages()[0]->getId();
        $fullMessage = $gmail->users_messages->get('me', $msgId, ['format' => 'full']);

        $payload = $fullMessage->getPayload();
        $headers = collect($payload->getHeaders());

        $from = optional($headers->firstWhere('name', 'From'))->value;
        $subject = optional($headers->firstWhere('name', 'Subject'))->value;

        $body = base64_decode($payload->getBody()->getData() ?? '');

        return response()->json([
            'from' => $from,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    public function startWatch()
    {
        $accessToken = json_decode(Storage::get('gmail/token.json'), true);

        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/gmail/credentials.json'));
        $client->addScope([
            Google_Service_Gmail::GMAIL_READONLY,
            Google_Service_Gmail::GMAIL_MODIFY
        ]);
        $client->setAccessToken($accessToken);

        // Refresh token if needed
        if ($client->isAccessTokenExpired()) {
            if (isset($accessToken['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                Storage::put('gmail/token.json', json_encode($client->getAccessToken()));
            } else {
                return response()->json(['error' => 'No refresh token available'], 401);
            }
        }

        try {
            $gmail = new Google_Service_Gmail($client);

            $watchRequest = new Google_Service_Gmail_WatchRequest([
                'labelIds' => ['INBOX'],
                'topicName' => 'projects/crm-mail-setup/topics/gmail-notify',
            ]);

            $watchResponse = $gmail->users->watch('me', $watchRequest);
            return response()->json(['success' => true, 'data' => $watchResponse]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
public function fetchEmailsSince($historyId)
{
    $accessToken = json_decode(Storage::get('gmail/token.json'), true);

    $client = new \Google_Client();
    $client->setAuthConfig(storage_path('app/gmail/credentials.json'));
    $client->addScope([
        \Google_Service_Gmail::GMAIL_READONLY,
        \Google_Service_Gmail::GMAIL_MODIFY,
    ]);
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        Log::error('Token expired while fetching history');
        return;
    }

    $gmail = new \Google_Service_Gmail($client);

    try {
        $historyResponse = $gmail->users_history->listUsersHistory('me', [
            'startHistoryId' => $historyId,
        ]);

        foreach ($historyResponse->getHistory() as $history) {
            foreach ($history->getMessages() as $message) {
                $msgId = $message->getId();
                $fullMessage = $gmail->users_messages->get('me', $msgId, ['format' => 'full']);
                // Parse headers/body here
                Log::info("📨 New Message ID: $msgId");
            }
        }
    } catch (\Exception $e) {
        Log::error("Failed to fetch history: " . $e->getMessage());
    }
}
}
