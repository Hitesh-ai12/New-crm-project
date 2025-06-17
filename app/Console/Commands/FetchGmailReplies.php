<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchGmailReplies extends Command
{
    protected $signature = 'gmail:fetch-replies';
    protected $description = 'Fetch latest email replies from Gmail using Gmail API';

    public function handle()
    {
        try {
         
            $storedAccessToken = json_decode(file_get_contents(storage_path('gmail-token.json')), true);

            $client = new \Google_Client();
            $client->setApplicationName('CRM Gmail Fetcher');
            $client->setAuthConfig(storage_path('credentials.json')); 
            $client->addScope('https://www.googleapis.com/auth/gmail.readonly');
            $client->setAccessToken($storedAccessToken);

            if ($client->isAccessTokenExpired()) {
                Log::warning('Gmail access token expired.');
                return;
            }

            $service = new \Google_Service_Gmail($client);
            $messages = $service->users_messages->listUsersMessages('me', [
                'labelIds' => ['INBOX'],
                'maxResults' => 5
            ]);

            foreach ($messages->getMessages() as $message) {
                $fullMessage = $service->users_messages->get('me', $message->getId(), ['format' => 'full']);
                $payload = $fullMessage->getPayload();
                $headers = collect($payload->getHeaders());

                $from = $headers->firstWhere('name', 'From')->value ?? '';
                $subject = $headers->firstWhere('name', 'Subject')->value ?? '';
                $body = base64_decode($payload->getBody()->getData() ?? '');

                Log::info("📥 New Email:");
                Log::info("From: $from");
                Log::info("Subject: $subject");
                Log::info("Body: $body");
            }

            $this->info("✅ Email replies fetched successfully.");
        } catch (\Exception $e) {
            Log::error('❌ Error fetching Gmail replies: ' . $e->getMessage());
            $this->error('Something went wrong. Check logs.');
        }
    }
}
