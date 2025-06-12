<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\EmailReply; 
use App\Models\Lead;
use Illuminate\Support\Carbon;

class FetchEmailReplies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-email-replies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    $client = Client::account('default');
    $client->connect();

    $inbox = $client->getFolder('INBOX');
    $messages = $inbox->query()->unseen()->get();

    foreach ($messages as $msg) {
        $from = $msg->getFrom()[0]->mail;
        $to = $msg->getTo()[0]->mail;
        $subject = $msg->getSubject();
        $messageBody = $msg->getHTMLBody(true) ?? $msg->getTextBody();
        $receivedAt = $msg->getDate();

        $lead = Lead::where('email', $from)->first();

        EmailReply::create([
            'user_id' => optional($lead)->user_id,
            'lead_id' => optional($lead)->id,
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $messageBody,
            'received_at' => Carbon::parse($receivedAt),
        ]);
    }

    $this->info("Fetched " . count($messages) . " new replies.");
    }
    
}
