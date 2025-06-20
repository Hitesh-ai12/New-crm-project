<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Mail\CrmMailable; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Template;
use App\Models\Lead;
use Webklex\IMAP\Facades\Client;
use App\Models\EmailLog;
use App\Models\SentEmail;
use Carbon\Carbon;
use App\Models\EmailReply;



class EmailController extends Controller
{
public function sendEmail(Request $request)
{
    $request->validate([
        'to' => 'required|array',
        'to.*' => 'email',
        'subject' => 'required|string',
        'message' => 'required|string',
        'template_id' => 'nullable|exists:templates,id',
        'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx',
        'user_id' => 'required|integer',
        'lead_ids' => 'required|array',
    ]);

    $from = $request->input('from', config('mail.from.address'));
    $userId = $request->input('user_id');
    $leadIdMap = array_flip($request->input('lead_ids'));

    $subjectTemplate = $request->input('subject');
    $messageTemplate = $request->input('message');
    $attachments = $request->file('attachments', []);
    $attachmentPaths = [];

    // Upload attachments
    foreach ($attachments as $file) {
        $path = $file->store('email_attachments', 'public');
        $attachmentPaths[] = $path;
    }

    // Send to each recipient
    foreach ($request->input('to') as $email) {
        $lead = Lead::where('email', $email)->first();

        if (!$lead || !isset($leadIdMap[$lead->id])) {
            continue;
        }

        $placeholders = [
            '{{first_name}}' => $lead->first_name,
            '{{last_name}}' => $lead->last_name ?? '',
            '{{email}}' => $lead->email,
            '{{phone}}' => $lead->phone ?? '',
            '{{city}}' => $lead->city ?? '',
        ];

        $personalizedSubject = strtr($subjectTemplate, $placeholders);
        $personalizedMessage = strtr($messageTemplate, $placeholders);

        try {
            // Send email
            Mail::to($email)->send(new CrmMailable([
                'from' => $from,
                'to' => $email,
                'subject' => $personalizedSubject,
                'message' => $personalizedMessage,
                'attachments' => $attachments,
                'attachmentPaths' => $attachmentPaths,
            ]));

            // Log in email_logs
            EmailLog::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'to' => $email,
                'from' => $from,
                'subject' => $personalizedSubject,
                'message' => $personalizedMessage,
                'direction' => 'sent',
                'sent_at' => now(),
                'attachments' => json_encode($attachmentPaths),
            ]);

            // Log in sent_emails
            SentEmail::create([
                'user_id' => $userId,
                'lead_id' => $lead->id,
                'from' => $from,
                'to' => $email,
                'subject' => $personalizedSubject,
                'message' => $personalizedMessage,
                'attachments' => json_encode($attachmentPaths),
            ]);
        } catch (\Exception $e) {
            \Log::error("❌ Failed to send email to {$email}: " . $e->getMessage());
            continue;
        }
    }

    return response()->json(['message' => '✅ Emails sent and stored successfully!']);
}



public function getSentEmails(Request $request)
    {
        $userId = $request->user()->id; // uses auth()

        $emails = SentEmail::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'lead_id', 'to', 'subject', 'message', 'created_at']);

        return response()->json($emails);
    }

    public function fetchReplies()
    {
        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->from('someone@example.com')->unseen()->get();

        foreach ($messages as $message) {
            echo "Subject: ".$message->getSubject()."<br>";
            echo "Body: ".$message->getTextBody()."<hr>";
        }
    } 

    public function inbox()
    {
        $replies = EmailReply::latest()->get();
        return view('email.inbox', compact('replies'));
    }

    public function sendReply(Request $request)
    {
        $data = $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        Mail::to($data['to'])->send(new \App\Mail\LeadReply($data['subject'], $data['message']));

        return response()->json(['status' => 'sent']);
    }

    public function getReplies(Request $request, $leadEmail)
    {
        $client = new Client([
            'host' => 'imap.yourmailserver.com',
            'port' => 993,
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => 'your-email@example.com',
            'password' => 'your-password',
            'protocol' => 'imap',
        ]);

        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->from($leadEmail)->all()->recent()->get();

        $replies = [];

        foreach ($messages as $message) {
            $replies[] = [
                'subject' => $message->getSubject(),
                'from' => $message->getFrom()[0]->mail,
                'date' => $message->getDate()->format('Y-m-d H:i:s'),
                'body' => $message->getTextBody() ?: $message->getHtmlBody(),
            ];
        }

        return response()->json($replies);
    }

    public function getEmailTimeline(Request $request, $leadEmail)
    {
        $sentEmails = SentEmail::where('to', $leadEmail)
            ->get()
            ->map(function ($email) {
                return [
                    'type' => 'email',
                    'direction' => 'sent',
                    'title' => 'Sent Email',
                    'description' => $email->subject,
                    'date' => $email->created_at->format('Y-m-d'),
                    'time' => $email->created_at->format('h:i A'),
                ];
            });

        $replies = EmailReply::where('from', $leadEmail)
            ->get()
            ->map(function ($reply) {
                return [
                    'type' => 'email',
                    'direction' => 'received',
                    'title' => 'Reply',
                    'description' => $reply->subject,
                    'date' => Carbon::parse($reply->received_at)->format('Y-m-d'),
                    'time' => Carbon::parse($reply->received_at)->format('h:i A'),
                ];
            });

        $combined = $sentEmails->merge($replies)->sortByDesc('date')->values();

        return response()->json($combined->isNotEmpty() ? $combined : [['title' => 'No emails found']]);
    }

    public function getEmailLogs(Lead $lead)
    {
        $logs = $lead->emailLogs()->latest()->get();

        return response()->json($logs);
    }

    public function getReceivedEmails(Request $request)
    {
        $receivedReplies = EmailReply::orderBy('received_at', 'desc')->get();

        $formattedReplies = $receivedReplies->map(function ($reply) {
            $receivedAt = $reply->received_at ? \Carbon\Carbon::parse($reply->received_at) : null;

            return [
                'id' => $reply->id,
                'type' => 'email',
                'direction' => 'received',
                'title' => 'Reply Received',
                'description' => $reply->subject ?? '(No Subject)',
                'body_plain' => strip_tags($reply->message ?? ''),
                'body_html' => $reply->message ?? '',
                'from' => $reply->from ?? '',
                'to' => $reply->to ?? '',
                'date' => $receivedAt ? $receivedAt->format('Y-m-d') : '',
                'time' => $receivedAt ? $receivedAt->format('h:i A') : '',
                'attachments' => $reply->attachments ?? [],
            ];
        });

        return response()->json($formattedReplies);
    }


}
