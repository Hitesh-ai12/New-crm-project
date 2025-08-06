<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Mail\ComposeEmail;
use Illuminate\Http\Request;
use App\Models\LeadActionPlanAssignment;
use App\Models\Automation\AutomationActionPlan;
use App\Models\Automation\AutomationAction;
use App\Models\Lead;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
                'last_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:leads,email|max:255',
                'phone' => 'required|regex:/^\+\d{10,15}$/|unique:leads,phone',
                'user_id' => 'nullable|exists:users,id',

                // General Info
                'tag' => 'nullable|string|max:255',
                'stage' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'lead_source' => 'nullable|string|max:255',

                // Alerts & Plans
                'new_listing_alert' => 'nullable|string',
                'lead_basic_details' => 'nullable|string',
                'neighbourhood_alert' => 'nullable|string',
                'open_house_alert' => 'nullable|string',
                'price_drop_alert' => 'nullable|string',
                'action_plans' => 'nullable|string',

                // JSON fields
                'files' => 'nullable|json',
                'tasks' => 'nullable|json',
                'appointments' => 'nullable|json',

                // Address
                'city' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'house_number' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'address_line1' => 'nullable|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',

                // Additional Info
                'notes' => 'nullable|string',
                'dob' => 'nullable|date',
                'gender' => 'nullable|string|max:20',
                'interested_in' => 'nullable|string|max:255',
                'preferred_contact_time' => 'nullable|string|max:255',

                // Social
                'facebook' => 'nullable|url',
                'instagram' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'whatsapp' => 'nullable|string|max:20',
                'twitter' => 'nullable|url',
            ]);

            $validatedData['user_id'] = $validatedData['user_id'] ?? auth()->id();

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


        public function update(Request $request, $id)
        {
            $lead = Lead::findOrFail($id); 

            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:leads,email,' . $id,
                'phone' => 'required|regex:/^\+\d{10,15}$/|unique:leads,phone,' . $id,
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
                'house_number' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'province' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:20',
                'user_id' => 'nullable|exists:users,id',
                'notes' => 'nullable|string',
                'dob' => 'nullable|date',
                'facebook' => 'nullable|url',
                'instagram' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'whatsapp' => 'nullable|string',
                'twitter' => 'nullable|url',
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


    /**
     * Assigns action plans to selected leads.
     */
    public function assignActionPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_plan_ids' => 'required|array|min:1',
            'action_plan_ids.*' => 'integer|exists:automation_action_plans,id',
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();

        try {
            $assignmentsCount = 0;
            foreach ($validated['lead_ids'] as $leadId) {
                foreach ($validated['action_plan_ids'] as $actionPlanId) {
                    $actionPlan = AutomationActionPlan::with('actions')->find($actionPlanId);
                    
                    if (!$actionPlan) {
                        continue;
                    }
                    
                    $assignment = LeadActionPlanAssignment::firstOrNew([
                        'lead_id' => $leadId,
                        'action_plan_id' => $actionPlanId,
                    ]);

                    // अगर असाइनमेंट नया है या 'completed' है, तो उसे अपडेट करें
                    if (!$assignment->exists || $assignment->status === 'completed') {
                        $firstAction = $actionPlan->actions->sortBy('step_order')->first();
                        $nextActionDueAt = null;
                        $currentActionId = null;

                        if ($firstAction) {
                            $delayDays = $firstAction->delay_days;
                            $nextActionDueAt = now()->addDays($delayDays);
                            $currentActionId = $firstAction->id;
                        }

                        $assignment->status = 'active';
                        $assignment->current_action_id = $currentActionId;
                        $assignment->next_action_due_at = $nextActionDueAt;
                        $assignment->assigned_at = now();
                        $assignment->save();
                        $assignmentsCount++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => "{$assignmentsCount} Action Plan(s) assigned to leads successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to assign action plans to leads.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel',
    ]);

    $file = $request->file('file');
    $handle = fopen($file->getRealPath(), 'r');

    if (!$handle) {
        return response()->json(['message' => 'Failed to open file.'], 400);
    }

    $header = fgetcsv($handle);
    if (!$header) {
        return response()->json(['message' => 'CSV is empty or header missing.'], 400);
    }

    $imported = 0;
    $updated = 0;
    $skipped = 0;

while (($row = fgetcsv($handle)) !== false) {
    if (count($header) !== count($row)) {
        logger()->info('Row skipped due to header/row count mismatch', [
            'header_count' => count($header),
            'row_count' => count($row),
            'row_data' => $row,
        ]);
        $skipped++;
        continue;
    }

    $data = array_combine($header, $row);
    if (!$data) {
        logger()->info('Row skipped due to array_combine failure', ['row' => $row]);
        $skipped++;
        continue;
    }

    $data = array_map('trim', $data);
    logger()->info('Parsed CSV Row:', $data); // ✅ Print each row

    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;

    if (!$email || !$phone) {
        logger()->info('Row skipped due to missing email/phone', $data);
        $skipped++;
        continue;
    }

    $lead = Lead::where('email', $email)
        ->orWhere('phone', $phone)
        ->first();

    // Normalize JSON fields if needed
    foreach (['files', 'tasks', 'appointments'] as $jsonField) {
        if (isset($data[$jsonField]) && $data[$jsonField] === '') {
            $data[$jsonField] = null;
        }
    }

    $validator = Validator::make($data, [
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
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
        'house_number' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'zip_code' => 'nullable|string|max:20',
        'notes' => 'nullable|string',
        'dob' => 'nullable|date',
        'facebook' => 'nullable|url',
        'instagram' => 'nullable|url',
        'linkedin' => 'nullable|url',
        'whatsapp' => 'nullable|string',
        'twitter' => 'nullable|url',
        'middle_name' => 'nullable|string|max:255',
        'source' => 'nullable|string|max:255',
        'referred_by' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:100',
        'gender' => 'nullable|string|max:100',
        'marital_status' => 'nullable|string|max:100',
    ]);

    if ($validator->fails()) {
        $errors = [];
        foreach ($validator->errors()->messages() as $field => $messages) {
            foreach ($messages as $msg) {
                $errors[] = "Field: '$field' => $msg";
            }
        }

        logger()->warning('Validation failed', [
            'row' => $data,
            'errors_detailed' => $errors,
        ]);

        $skipped++;
        continue;
    }

        $validated = $validator->validated();
        $validated['user_id'] = auth()->id();

        if ($lead) {
            $lead->update($validated);
            $updated++;
        } else {
            Lead::create($validated);
            $imported++;
        }
    }

    fclose($handle);

    return response()->json([
        'message' => 'Import completed.',
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
    ]);
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
    }
