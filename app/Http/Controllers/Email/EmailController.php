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
use App\Models\Email;
use App\Models\SentEmail;
use Carbon\Carbon;
use App\Models\EmailReply;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;





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
                Email::create([
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

            } catch (\Exception $e) {
                \Log::error("❌ Failed to send email to {$email}: " . $e->getMessage());
                continue;
            }
        }

        return response()->json(['message' => '✅ Emails sent and stored successfully!']);
    }



    public function getSentEmails(Request $request)
    {
        $userId = $request->user()->id; 

        $emails = Email::where('user_id', $userId)
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
        $replies = Email::latest()->get();
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
        $sentEmails = Email::where('to', $leadEmail)
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

        $replies = Email::where('from', $leadEmail)
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
        $receivedReplies = Email::orderBy('sent_at', 'desc')->where('direction', 'received')->get();

        $formattedReplies = $receivedReplies->map(function ($reply) {
            $receivedAt = $reply->sent_at ? \Carbon\Carbon::parse($reply->sent_at) : null;

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

public function getLeadMessages(Request $request, $leadId)
{
    $page = $request->input('page', 1);
    $perPage = $request->input('per_page', 20);
    $offset = ($page - 1) * $perPage;

    // Combine email_logs and email_replies
    $combined = DB::table('email_logs')
        ->select(
            'id',
            'lead_id',
            'from',
            'to',
            'subject',
            'message',
            'attachments',
            DB::raw("'sent' as direction"),
            'created_at',
            'sent_at as date_time'
        )
        ->where('lead_id', $leadId)

        ->unionAll(
            DB::table('email_replies')
                ->select(
                    'id',
                    'lead_id',
                    'from',
                    'to',
                    'subject',
                    'message',
                    DB::raw('NULL as attachments'),
                    DB::raw("'received' as direction"),
                    'created_at',
                    'received_at as date_time'
                )
                ->where('lead_id', $leadId)
        );

    // Wrap the union in a subquery to sort and paginate
    $results = DB::table(DB::raw("({$combined->toSql()}) as combined"))
        ->mergeBindings($combined)
        ->orderByDesc('date_time')
        ->offset($offset)
        ->limit($perPage)
        ->get();

    // Total count for pagination
    $totalCount = DB::table('email_logs')->where('lead_id', $leadId)->count() +
                  DB::table('email_replies')->where('lead_id', $leadId)->count();

    // Format response for frontend
    $formatted = $results->map(function ($item) {
        return [
            'type' => 'email',
            'direction' => $item->direction,
            'subject' => $item->subject,
            'message' => $item->message,
            'from' => $item->from,
            'to' => $item->to,
            'attachments' => $item->attachments ? json_decode($item->attachments) : [],
            'date' => $item->date_time ? Carbon::parse($item->date_time)->format('Y-m-d') : '',
            'time' => $item->date_time ? Carbon::parse($item->date_time)->format('H:i:s') : '',
        ];
    });

    // Paginate manually
    $paginated = new LengthAwarePaginator(
        $formatted,
        $totalCount,
        $perPage,
        $page,
        ['path' => url()->current()]
    );

    return response()->json($paginated);
}

    // Controller
    public function getLeadEmails($leadId)
    {
        $emails = Email::where('lead_id', $leadId)
            ->orderByRaw('COALESCE(sent_at, created_at) ASC')
            ->get()
            ->map(function ($email) {
                $timestamp = $email->sent_at ?? $email->created_at;

                return [
                    'direction' => $email->direction,
                    'subject' => $email->subject ?? '(No Subject)',
                    'description' => strip_tags($email->message ?? ''),
                    'time' => optional($timestamp)->format('H:i'),
                    'date' => optional($timestamp)->format('Y-m-d'),
                    'from' => $email->from,
                    'to' => $email->to,
                ];
            });

        return response()->json($emails);
    }


}
