<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\EmailReply;
use App\Models\SentEmail;
use App\Models\Lead;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ListenForNewEmails extends Command
{
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listens for new email replies in real-time using IMAP IDLE.';

    /**
     * Execute the console command.
     */
// app/Console/Commands/ListenForNewEmails.php
// ...
public function handle()
{
    $this->info('Starting IMAP IDLE listener...');

    try {
        $client = Client::account('default');
        $client->connect();

        if (!$client->isConnected()) {
            $this->error('Failed to connect to IMAP server. Please check your IMAP configuration.');
            return self::FAILURE;
        }

        $this->info('Connected to IMAP server. Listening for new messages in INBOX...');

        $folder = $client->getFolder('INBOX');

        // --- TEMPORARY DEBUGGING: Fetch ALL messages to test processing ---
        $this->info('Attempting to fetch all messages for initial test...');
        $allMessages = $folder->query()->all()->get(); // Get all messages, not just unread
        $this->info('Found ' . count($allMessages) . ' messages in INBOX.');
        foreach ($allMessages as $message) {
            try {
                $this->info("Processing historical message: " . $message->getSubject());
                $this->processEmailMessage($message);
                // Optionally mark as read if you want to test that
                // $message->markAsRead();
            } catch (\Exception $e) {
                $this->error("Error processing historical message '{$message->getSubject()}': " . $e->getMessage());
                \Log::error("Error processing historical email: " . $e->getMessage(), ['message_id' => $message->getUid(), 'subject' => $message->getSubject(), 'trace' => $e->getTraceAsString()]);
            }
        }
        $this->info('Finished processing historical messages.');
        // --- END TEMPORARY DEBUGGING ---


        $this->info('Starting IMAP IDLE for real-time notifications...');
        $folder->idle(function ($message) {
            $this->info('New message detected via IDLE! Subject: ' . $message->getSubject());
            try {
                $this->processEmailMessage($message);
                $message->markAsRead(); 
            } catch (\Exception $e) {
                $this->error("Error processing new IDLE message '{$message->getSubject()}': " . $e->getMessage());
                \Log::error("Error processing new IDLE email: " . $e->getMessage(), ['message_id' => $message->getUid(), 'subject' => $message->getSubject(), 'trace' => $e->getTraceAsString()]);
            }
        });

        $client->disconnect();
        $this->info('IMAP IDLE listener stopped unexpectedly. It will be restarted by Supervisor (in production).');

        return self::SUCCESS;

    } catch (\Exception $e) {
        $this->error('An unhandled error occurred in IMAP listener: ' . $e->getMessage());
        \Log::critical('IMAP IDLE Critical Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return self::FAILURE;
    }
}
// ... rest of processEmailMessage ...

    /**
     * Processes an individual email message and stores it as a reply.
     *
     * @param \Webklex\IMAP\Message $message
     * @return void
     */
    protected function processEmailMessage($message)
    {
        $fromEmail = $message->getFrom()[0]->mail ?? null;

        if (!$fromEmail) {
            $this->warn('Skipping message with no sender email. Message ID: ' . $message->getUid());
            return;
        }

        // 1. Try to find a lead based on the sender's email
        $lead = Lead::where('email', $fromEmail)->first();

        // 2. Try to link to a previously sent email using Message-ID headers
        $inReplyTo = $message->getHeader('in-reply-to')->first() ?? null;
        $references = $message->getHeader('references')->first() ?? null;

        $sentEmail = null;
        if ($inReplyTo) {
            // Find a SentEmail where its Message-ID matches the In-Reply-To header
            $sentEmail = SentEmail::where('message_id', $inReplyTo)->first();
        }

        // If no match by In-Reply-To, try References header (which contains a chain of message IDs)
        if (!$sentEmail && $references) {
            $referenceIds = explode(' ', $references);
            foreach ($referenceIds as $refId) {
                // Find any sent email that was part of this conversation chain
                $sentEmail = SentEmail::where('message_id', $refId)->first();
                if ($sentEmail) {
                    break; // Found a link, no need to check further references
                }
            }
        }

        // 3. If a lead wasn't found by fromEmail, but we linked to a sent email that has a lead, use that lead
        if (!$lead && $sentEmail && $sentEmail->lead) {
            $lead = $sentEmail->lead;
        }

        // If after all checks, no lead is associated, we skip logging this reply.
        // You might want to log these as "unmatched" emails for manual review.
        if (!$lead) {
            $this->warn("Skipping message from {$fromEmail} as no matching lead found. Subject: " . $message->getSubject());
            return;
        }

        // Handle attachments
        $attachments = [];
        foreach ($message->getAttachments() as $attachment) {
            try {
                $originalFilename = $attachment->getName();
                $uniqueFilename = Str::uuid() . '_' . $originalFilename;
                $path = 'email_attachments/' . $uniqueFilename;

                Storage::disk('public')->put($path, $attachment->getContent());
                $attachments[] = Storage::url($path); // Store public URL or path

                $this->info("Saved attachment: {$originalFilename} to {$path}");
            } catch (\Exception $e) {
                $this->error("Failed to save attachment '{$attachment->getName()}' for message '{$message->getSubject()}': " . $e->getMessage());
                \Log::error("Attachment save error: " . $e->getMessage(), ['attachment' => $attachment->getName(), 'message_id' => $message->getUid()]);
            }
        }

        // Store the email reply in the database
        EmailReply::create([
            'lead_id'       => $lead->id,
            'sent_email_id' => $sentEmail ? $sentEmail->id : null,
            'message_id'    => $message->getHeader('message-id')->first() ?? null,
            'in_reply_to'   => $inReplyTo,
            'references'    => $references,
            'from'          => $fromEmail,
            // Combine all 'to' recipients into a single string for storage
            'to'            => implode(', ', array_column($message->getTo(), 'mail')),
            'subject'       => $message->getSubject(),
            'body_plain'    => $message->getTextBody(),
            'body_html'     => $message->getHTMLBody(),
            // Ensure the date is parsed correctly into a Carbon instance
            'received_at'   => Carbon::parse($message->getDate()),
            'attachments'   => $attachments, // Stored as JSON
            'is_read'       => $message->getFlags()->contains('Seen'), // Initial read status
        ]);

        $this->info("Successfully processed and saved reply from {$fromEmail} for lead ID {$lead->id}.");

        // Optional: Trigger an event or push notification to frontend
        // event(new \App\Events\NewEmailReplyArrived($lead->id, $emailReply));
    }
}
