<?php

namespace App\Services;

use App\Models\GmailToken;
use Carbon\Carbon;
use Google_Client;

class GmailService
{
    public static function getGoogleClient($userId): Google_Client
    {
        $token = GmailToken::where('user_id', $userId)->firstOrFail();

        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $client->addScope([
            \Google_Service_Gmail::GMAIL_READONLY,
            \Google_Service_Gmail::GMAIL_MODIFY,
        ]);

        // Set current access token
        $client->setAccessToken([
            'access_token'  => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'expires_in'    => Carbon::now()->diffInSeconds($token->expires_at, false),
            'created'       => time(),
        ]);

        // Refresh if expired
        if ($client->isAccessTokenExpired()) {
            if (!$token->refresh_token) {
                throw new \Exception('Refresh token is missing for user ID: ' . $userId);
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($token->refresh_token);

            if (isset($newToken['error'])) {
                throw new \Exception('Failed to refresh token: ' . $newToken['error']);
            }

            // Update DB with refreshed access token and new expiry
            $token->update([
                'access_token' => $newToken['access_token'],
                'expires_at'   => Carbon::now()->addSeconds($newToken['expires_in']),
            ]);

            // Set refreshed token to client
            $client->setAccessToken($newToken);
        }

        return $client;
    }
}
