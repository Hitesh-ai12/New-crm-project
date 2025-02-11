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
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'template_id' => 'nullable|exists:templates,id', // ✅ Ensure template_id exists
        ]);

        // Fetch the template if provided
        $template = null;
        if ($request->filled('template_id')) {
            $template = Template::find($request->template_id);
        }

        // Prepare email data
        $from = $request->input('from', 'hiteshpandey732195@gmail.com');
        $to = $request->input('to');
        $subject = $template ? $template->subject : $request->input('subject'); // Use template subject if available
        $message = $template ? $template->content : $request->input('message'); // Use template content if available
        $attachments = $request->file('attachments') ?? [];

        $emailData = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'attachments' => $attachments,
        ];

        // Send email using CrmMailable
        Mail::to($to)->send(new CrmMailable($emailData));

        return response()->json(['message' => 'Email sent successfully!'], 200);
    }
    
}
