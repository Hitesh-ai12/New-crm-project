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
use Illuminate\Support\Str;

class GmailWebhookController extends Controller
{

    public function handle(Request $request)
    {
        // Decode the incoming message
        $data = $request->input('message.data');
        $decoded = json_decode(base64_decode($data), true);

        Log::info('📬 Pub/Sub Data:', $decoded);

        $historyId = $decoded['historyId'] ?? null;

        Log::info("🔁 Gmail history ID: $historyId");

        // Load saved access token
        $accessToken = json_decode(Storage::get('gmail/token.json'), true);

        // Setup Google Client
        $client = new \Google_Client();
        $client->setAuthConfig(storage_path('app/gmail/credentials.json'));
        $client->addScope([
            \Google_Service_Gmail::GMAIL_READONLY,
            \Google_Service_Gmail::GMAIL_MODIFY
        ]);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            Log::error('❌ Token expired');
            return response()->json(['error' => 'Token expired'], 401);
        }

        $gmail = new \Google_Service_Gmail($client);

        // Fetch latest inbox message
        $messages = $gmail->users_messages->listUsersMessages('me', [
            'labelIds' => ['INBOX'],
            'maxResults' => 1
        ]);

        if (count($messages->getMessages()) === 0) {
            Log::info('📭 No new email found.');
            return response()->json(['message' => 'No new email found.']);
        }

        $msgId = $messages->getMessages()[0]->getId();
        $message = $gmail->users_messages->get('me', $msgId, ['format' => 'full']);
        $payload = $message->getPayload();
        $headers = collect($payload->getHeaders());

        // Extract email headers
        $from = optional($headers->firstWhere('name', 'From'))->value;
        // Extract email from "Name <email>" format
        preg_match('/<(.+)>/', $from, $matches);
        $fromEmail = isset($matches[1]) ? $matches[1] : $from;
        // Normalize
        $fromEmail = trim(strtolower($fromEmail));

        $to = optional($headers->firstWhere('name', 'To'))->value;
        $subject = optional($headers->firstWhere('name', 'Subject'))->value;
        $receivedAt = Carbon::createFromTimestamp($message->getInternalDate() / 1000);

        // Decode email body
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

        $lead = Lead::whereRaw('LOWER(email) = ?', [$fromEmail])->first();

        if (!$lead) {
            Log::warning("❌ No matching lead found for email: $fromEmail");
            return response()->json(['message' => 'No matching lead found.'], 404);
        }

        // Store email reply only if lead matched
        EmailReply::create([
            'user_id' => $lead->user_id,
            'lead_id' => $lead->id,
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $body,
            'received_at' => $receivedAt,
        ]);

        Log::info("✅ Email stored for lead ID: {$lead->id}, user ID: {$lead->user_id}");

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
        $client->addScope([
            \Google_Service_Gmail::GMAIL_READONLY,
            \Google_Service_Gmail::GMAIL_MODIFY,
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setAccessToken($accessToken);

        // 🔄 Refresh token if expired
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

                if (isset($newToken['error'])) {
                    return response()->json(['error' => '🔒 Failed to refresh access token.'], 401);
                }

                Storage::put('gmail/token.json', json_encode($client->getAccessToken()));
            } else {
                return response()->json(['error' => '🔒 No refresh token available. Please re-authenticate.'], 401);
            }
        }

        $gmail = new \Google_Service_Gmail($client);

        // 📨 Get latest message
        $messages = $gmail->users_messages->listUsersMessages('me', [
            'labelIds' => ['INBOX'],
            'maxResults' => 1,
        ]);

        if (count($messages->getMessages()) === 0) {
            return response()->json(['message' => '📭 No new emails.']);
        }

        $msgId = $messages->getMessages()[0]->getId();
        $fullMessage = $gmail->users_messages->get('me', $msgId, ['format' => 'full']);

        $payload = $fullMessage->getPayload();
        $headers = collect($payload->getHeaders());

        $from = optional($headers->firstWhere('name', 'From'))->value;
        $subject = optional($headers->firstWhere('name', 'Subject'))->value;

        // 📝 Extract plain text content
        $body = '';
        $parts = $payload->getParts();
        if ($parts && count($parts)) {
            foreach ($parts as $part) {
                if ($part->getMimeType() === 'text/plain') {
                    $body = base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
                    break;
                }
            }
        } else {
            $body = base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
        }

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
