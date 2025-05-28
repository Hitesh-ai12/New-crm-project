<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Mail\CrmMailable; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Template;


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
            'attachment_paths' => 'nullable|string', // JSON encoded paths from template
        ]);
    
        $from = $request->input('from', 'default@example.com');
        $to = $request->input('to');
        $subject = $request->input('subject');
        $message = $request->input('message');
        $attachments = $request->file('attachments', []);
        $attachmentPaths = json_decode($request->input('attachment_paths', '[]'), true) ?? [];
    
        // Compose email data
        $emailData = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'attachments' => $attachments,
            'attachmentPaths' => $attachmentPaths,
        ];
    
        Mail::to($to)->send(new CrmMailable($emailData));
    
        return response()->json(['message' => 'Email sent successfully!']);
    }
    
    
    
}
