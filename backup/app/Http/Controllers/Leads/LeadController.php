<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Response;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadMail;

class LeadController extends Controller
{
    public function index()
    {
        return Lead::all();
    }

    public function store(Request $request)
    {
       
    // Validate the incoming request
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|regex:/^\d{10}$/',
            'tag' => 'required|string', // Ensure this field is expected to be a string
            'stage' => 'required|string', // Ensure this field is expected to be a string
        ]);

        // Create a new Lead
        $lead = Lead::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'tag' => $request->tag,
            'stage' => $request->stage,
            'source' => 'direct',
        ]);

        // Return the created Lead as a JSON response
        return response()->json($lead, 201);
    }

      // Function to delete selected leads
    public function deleteLeads(Request $request)
    {

        $leadIds = $request->input('lead_ids');
        Lead::whereIn('id', $leadIds)->delete();

        return response()->json(['message' => 'Selected leads deleted successfully.']);
    }

    public function exportLeads(Request $request)
    {
        $leadIds = $request->input('lead_ids');

        // Retrieve the leads to be exported
        $leads = Lead::whereIn('id', $leadIds)->get();

        // Generate CSV
        $csv = "ID, Name, Email, Phone\n";
        foreach ($leads as $lead) {
            $csv .= "{$lead->id}, {$lead->name}, {$lead->email}, {$lead->phone}\n";
        }

        $response = Response::make($csv);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="leads.csv"');

        return $response;
    }


    public function sendSms(Request $request)
    {
        $leadIds = $request->input('lead_ids');
        $message = $request->input('message');

        $leads = Lead::whereIn('id', $leadIds)->get();

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

        foreach ($leads as $lead) {
            $twilio->messages->create(
                $lead->phone,
                [
                    'from' => env('TWILIO_PHONE_NUMBER'),
                    'body' => $message
                ]
            );
        }

        return response()->json(['success' => 'SMS sent successfully.']);
    }



    public function sendEmail(Request $request)
    {
        $leadIds = $request->input('lead_ids');
        $message = $request->input('message');

        $leads = Lead::whereIn('id', $leadIds)->get();

        foreach ($leads as $lead) {
            Mail::to($lead->email)->send(new LeadMail($message));
        }

        return response()->json(['success' => 'Emails sent successfully.']);
    }
}
