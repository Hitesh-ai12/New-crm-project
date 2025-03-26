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
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string',
                'email' => 'required|email|unique:leads|max:255',
                'phone' => 'required|regex:/^\d{10}$/|unique:leads',
                'tag' => 'nullable|string|max:255',
                'stage' => 'nullable|string|max:255',
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
                'user_id' => 'nullable|exists:users,id', // user_id optional but must exist in users table
            ]);
        
            // Agar `user_id` request se aaya hai to wahi use karein, warna logged-in user ka ID use karein
            $validatedData['user_id'] = $validatedData['user_id'] ?? auth()->id();
        
            // Ensure user_id is not null (must be a registered user)
            if (!$validatedData['user_id']) {
                return response()->json([
                    'message' => 'User ID is required and must be a registered user.',
                ], 400);
            }
        
            $lead = Lead::create($validatedData);
        
            return response()->json([
                'message' => 'Lead created successfully.',
                'data' => $lead,
            ], 201);
        }
        
    
        public function exportLeads(Request $request)
        {
            $leadIds = $request->input('lead_ids');
    
            $leads = Lead::whereIn('id', $leadIds)->get();
    
            $csv = "ID, Name, Email, Phone\n";
            foreach ($leads as $lead) {
                $csv .= "{$lead->id}, {$lead->name}, {$lead->email}, {$lead->phone}\n";
            }
    
            $response = Response::make($csv);
            $response->header('Content-Type', 'text/csv');
            $response->header('Content-Disposition', 'attachment; filename="leads.csv"');
    
            return $response;
        }
    

        public function sendEmail(Request $request)
        {
            $request->validate([
                'lead_ids' => 'required|array',
                'message' => 'required|string',
                'subject' => 'required|string',
            ]);

            $subject = $request->input('subject');
            $content = $request->input('message');
            $leadIds = $request->input('lead_ids');

            foreach ($leadIds as $leadId) {
                $lead = Lead::find($leadId); 

                Mail::to($lead->email)->send(new ComposeEmail($subject, $content, $leadIds));
            }

            return response()->json(['message' => 'Emails sent successfully!']);
        }


        public function update(Request $request, $id)
        {
            $lead = Lead::findOrFail($id); 
        
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:leads,email,'.$id,
                'phone' => 'required|regex:/^\d{10}$/|unique:leads,phone,'.$id,
                'tag' => 'nullable|string|max:255',
                'stage' => 'nullable|string|max:255',             
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
        
            $lead->update($validatedData);
        
            return response()->json([
                'message' => 'Lead updated successfully.',
                'data' => $lead,
            ], 200);
        }
        public function show($id) {
            $lead = Lead::find($id);
        
            if (!$lead) {
                return response()->json(['message' => 'Lead not found'], 404);
            }
        
            return response()->json($lead);
        }
                
    }
