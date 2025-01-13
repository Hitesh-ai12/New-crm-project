<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Mail\ComposeEmail;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
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
            $userId = auth()->id();

            // Validate the incoming request
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string',
                'email' => 'required|email|unique:leads|max:255',
                'phone' => 'required|regex:/^\d{10}$/|unique:leads',
                'new_listing_alert' => 'nullable|string',
                'lead_basic_details' => 'nullable|string',
                'neighbourhood_alert' => 'nullable|string',
                'open_house_alert' => 'nullable|string',
                'price_drop_alert' => 'nullable|string',
                'action_plans' => 'nullable|string',
                'files' => 'nullable|json',
                'background' => 'nullable|string',
                'real_estate_newsletter' => 'nullable|string',
                'market_updates' => 'nullable|string',
                'city' => 'nullable|string|max:255',
                'tasks' => 'nullable|json',
                'appointments' => 'nullable|json',
            ]);

            // Add the user ID to the validated data
            $validatedData['user_id'] = $userId;
            $lead = Lead::create($validatedData);
            // Return a JSON response with the created lead
            return response()->json([
                'message' => 'Lead created successfully.',
                'data' => $lead,
            ], 201);
        }
    
        public function exportLeads(Request $request)
        {
            $leadIds = $request->input('lead_ids');
    
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
    
        /**
         * Send the composed email to selected leads.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function sendEmail(Request $request)
        {
            // Validate request data
            $request->validate([
                'lead_ids' => 'required|array',
                'message' => 'required|string',
                'subject' => 'required|string',
            ]);

            // Prepare data for the email
            $subject = $request->input('subject');
            $content = $request->input('message');
            $leadIds = $request->input('lead_ids');

            // Send the email to all leads (or customize this based on your application)
            foreach ($leadIds as $leadId) {
                $lead = Lead::find($leadId); // Assuming you have a Lead model

                // Send the email to the lead
                Mail::to($lead->email)->send(new ComposeEmail($subject, $content, $leadIds));
            }

            return response()->json(['message' => 'Emails sent successfully!']);
        }
         
    }
