<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class GmailWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->input('message.data');
        $decoded = json_decode(base64_decode($data), true);

        Log::info('📬 Pub/Sub Data:', $decoded);

        // Example: Pull historyId
        $historyId = $decoded['historyId'] ?? null;
        Log::info("🔁 Gmail history ID: $historyId");

        return response()->json(['status' => 'ok']);
    }

        public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->addScope([
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.modify'
        ]);
        $client->setRedirectUri(route('gmail.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setRedirectUri(route('gmail.callback'));

        if ($request->has('code')) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($request->get('code'));

            if (isset($accessToken['error'])) {
                return response()->json(['error' => $accessToken['error']], 500);
            }

            // Save token to a file
            File::put(storage_path('gmail-token.json'), json_encode($accessToken));
            return response()->json(['message' => '✅ Gmail token saved successfully!']);
        }

        return response()->json(['error' => 'No code returned'], 400);
    }
}
