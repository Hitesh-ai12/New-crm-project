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

class GmailWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->input('message.data');
        $decoded = json_decode(base64_decode($data), true);

        Log::info('📬 Pub/Sub Data:', $decoded);

        $historyId = $decoded['historyId'] ?? null;
        Log::info("🔁 Gmail history ID: $historyId");

        return response()->json(['status' => 'ok']);
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
                'topicName' => 'projects/crm-mail-setup/topics/gmail-push-topic',
            ]);

            $watchResponse = $gmail->users->watch('me', $watchRequest);
            return response()->json(['success' => true, 'data' => $watchResponse]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
