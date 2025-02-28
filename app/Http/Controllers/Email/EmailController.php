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
        // Validate the request
        $request->validate([
            'to' => 'required|array',  // ✅ Accept multiple emails as an array
            'to.*' => 'email',         // ✅ Validate each email
            'subject' => 'required|string',
            'message' => 'required|string',
            'template_id' => 'nullable|exists:templates,id',
        ]);
    
        // Fetch the template if provided
        $template = null;
        if ($request->filled('template_id')) {
            $template = Template::find($request->template_id);
        }
    
        // Prepare email data
        $from = $request->input('from', 'hiteshpandey732195@gmail.com');
        $to = $request->input('to'); // ✅ Now an array of emails
        $subject = $template ? $template->subject : $request->input('subject');
        $message = $template ? $template->content : $request->input('message');
        $attachments = $request->file('attachments') ?? [];
    
        $emailData = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'attachments' => $attachments,
        ];
    
        // ✅ Send email to multiple recipients
        Mail::to($to)->send(new CrmMailable($emailData));
    
        return response()->json(['message' => 'Email sent successfully!'], 200);
    }
    
    
}
