<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeadActionPlanAssignment;
use App\Models\Automation\AutomationAction;
use App\Models\Lead;
use App\Models\User; // Assuming User model is used for tasks, etc.
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComposeEmail; // Assuming you have an email Mailable
use App\Mail\LeadMail; // If you have another Mailable for leads
use App\Models\Template; // For fetching email/sms templates
use Carbon\Carbon; // For date/time calculations
use Illuminate\Support\Facades\DB; // For transactions

class ProcessLeadActionPlans extends Command
{
    /**
     * The name and signature of the console command.
     * कमांड को चलाने के लिए 'php artisan process:lead-actions' का उपयोग करें।
     * @var string
     */
    protected $signature = 'process:lead-actions';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Processes pending actions for assigned lead action plans.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Starting to process lead action plans.');

        // उन असाइनमेंट्स को फेच करें जो 'active' हैं और जिनका 'next_action_due_at' बीत चुका है या अभी है।
        $pendingAssignments = LeadActionPlanAssignment::where('status', 'active')
            ->whereNotNull('next_action_due_at') // Ensure a due date is set
            ->where('next_action_due_at', '<=', now())
            ->with(['lead', 'actionPlan.actions']) // Lead, ActionPlan, और उसके Actions को eager load करें
            ->get();

        $this->info("Found " . $pendingAssignments->count() . " pending assignments.");

        foreach ($pendingAssignments as $assignment) {
            DB::beginTransaction(); // हर असाइनमेंट के लिए ट्रांजेक्शन शुरू करें
            try {
                // सुनिश्चित करें कि लीड और एक्शन प्लान मौजूद हैं
                if (!$assignment->lead || !$assignment->actionPlan) {
                    Log::warning("Skipping assignment {$assignment->id}: Lead or Action Plan not found.");
                    $assignment->status = 'stopped'; // Invalid assignment को रोक दें
                    $assignment->save();
                    DB::commit();
                    continue;
                }

                // वर्तमान एक्शन को ढूंढें
                // अगर current_action_id null है, तो यह प्लान का पहला एक्शन होगा
                $currentAction = null;
                if ($assignment->current_action_id) {
                    $currentAction = $assignment->actionPlan->actions
                        ->firstWhere('id', $assignment->current_action_id);
                } else {
                    // अगर current_action_id null है, तो पहला एक्शन ढूंढें
                    $currentAction = $assignment->actionPlan->actions->sortBy('step_order')->first();
                }

                if (!$currentAction) {
                    Log::warning("Skipping assignment {$assignment->id}: No current action found or plan has no actions.");
                    $assignment->status = 'completed'; // अगर कोई एक्शन नहीं है, तो प्लान को पूरा मानें
                    $assignment->save();
                    DB::commit();
                    continue;
                }

                $this->info("Processing action '{$currentAction->type}' for Lead '{$assignment->lead->first_name}' from Plan '{$assignment->actionPlan->name}'");

                // एक्शन के प्रकार के आधार पर प्रोसेस करें
                switch ($currentAction->type) {
                    case 'Create Task':
                        $this->createTask($assignment->lead, $currentAction);
                        break;
                    case 'Send Email':
                        $this->sendEmail($assignment->lead, $currentAction);
                        break;
                    case 'Send Text':
                        $this->sendText($assignment->lead, $currentAction);
                        break;
                    case 'Change Stage':
                        $this->changeLeadStage($assignment->lead, $currentAction);
                        break;
                    case 'Add Note':
                        $this->addLeadNote($assignment->lead, $currentAction);
                        break;
                    case 'Add Tag(s)':
                        $this->addLeadTags($assignment->lead, $currentAction);
                        break;
                    case 'Remove Tag(s)':
                        $this->removeLeadTags($assignment->lead, $currentAction);
                        break;
                    case 'Remove all Tags':
                        $this->removeAllLeadTags($assignment->lead);
                        break;
                    case 'Pause all other Action plans':
                        $this->pauseAllOtherActionPlans($assignment->lead, $assignment->action_plan_id);
                        break;
                    case 'Pause Specific Action plan':
                        $this->pauseSpecificActionPlan($assignment->lead, $currentAction);
                        break;
                    case 'Repeat Action Plan':
                        // यह थोड़ा जटिल है, इसे बाद में देखें
                        Log::info("Repeat Action Plan not yet implemented for assignment {$assignment->id}.");
                        break;
                    case 'Assign Action Plan':
                        $this->assignAnotherActionPlan($assignment->lead, $currentAction);
                        break;
                    default:
                        Log::warning("Unknown action type '{$currentAction->type}' for assignment {$assignment->id}.");
                        break;
                }

                // अगले एक्शन को निर्धारित करें
                $nextAction = $assignment->actionPlan->actions
                    ->where('step_order', '>', $currentAction->step_order)
                    ->sortBy('step_order')
                    ->first();

                if ($nextAction) {
                    $assignment->current_action_id = $nextAction->id;
                    $assignment->next_action_due_at = now()->addDays($nextAction->delay_days);
                    $this->info("Next action for assignment {$assignment->id} is '{$nextAction->type}' due at {$assignment->next_action_due_at}.");
                } else {
                    // कोई और एक्शन नहीं है, प्लान पूरा हो गया
                    $assignment->status = 'completed';
                    $assignment->current_action_id = null;
                    $assignment->next_action_due_at = null;
                    $this->info("Action plan {$assignment->actionPlan->name} completed for Lead {$assignment->lead->first_name}.");
                }

                $assignment->save(); // असाइनमेंट स्टेटस और अगली ड्यू डेट सेव करें
                DB::commit(); // ट्रांजेक्शन कमिट करें

            } catch (\Exception $e) {
                DB::rollBack(); // एरर होने पर ट्रांजेक्शन रोलबैक करें
                Log::error("Error processing assignment {$assignment->id}: " . $e->getMessage());
                // असाइनमेंट को 'stopped' पर सेट करें ताकि यह फिर से प्रोसेस न हो
                $assignment->status = 'stopped';
                $assignment->save();
                $this->error("Error processing assignment {$assignment->id}: " . $e->getMessage());
            }
        }

        Log::info('Finished processing lead action plans.');
        $this->info('Action plan processing finished.');
    }

    /**
     * Action Type Handlers
     * इन मेथड्स में हर एक्शन टाइप का लॉजिक होगा।
     */

    private function createTask(Lead $lead, AutomationAction $action)
    {
        // यहाँ टास्क बनाने का लॉजिक लिखें
        // उदाहरण: Lead model में 'tasks' JSON field में जोड़ें, या एक dedicated Task model में नया रिकॉर्ड बनाएँ।
        // अगर आपके पास Task model है:
        // Task::create([
        //     'lead_id' => $lead->id,
        //     'name' => $action->task_name,
        //     'type' => $action->task_type,
        //     'due_date' => now()->addDays(1), // या कोई और लॉजिक
        //     'user_id' => $lead->user_id, // या असाइन करने वाले यूजर की ID
        // ]);

        // JSON field में जोड़ने का उदाहरण:
        $lead->addJsonTask([ // Assuming you have an addJsonTask helper on Lead model
            'name' => $action->task_name,
            'type' => $action->task_type,
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
        ]);
        Log::info("Task '{$action->task_name}' created for Lead {$lead->id}.");
    }

    private function sendEmail(Lead $lead, AutomationAction $action)
    {
        // यहाँ ईमेल भेजने का लॉजिक लिखें
        // Queue का उपयोग करें ताकि ईमेल बैकग्राउंड में भेजा जाए
        if ($action->email_template_id && $lead->email) {
            $template = Template::find($action->email_template_id);
            if ($template) {
                // Mail::to($lead->email)->queue(new ComposeEmail($template, $lead)); // अगर ComposeEmail Mailable है
                // Mail::to($lead->email)->queue(new LeadMail($template->subject, $template->content, $lead)); // अगर LeadMail Mailable है
                Log::info("Email '{$template->title}' queued for Lead {$lead->id}.");
            } else {
                Log::warning("Email template {$action->email_template_id} not found for Lead {$lead->id}.");
            }
        } else {
            Log::warning("Cannot send email for Lead {$lead->id}: No template ID or email address.");
        }
    }

    private function sendText(Lead $lead, AutomationAction $action)
    {
        // यहाँ SMS भेजने का लॉजिक लिखें
        if ($action->sms_template_id && $lead->phone) {
            $template = Template::find($action->sms_template_id); // Assuming SMS templates are also in 'templates' table
            if ($template) {
                // Twilio या कोई और SMS सर्विस का उपयोग करें
                // dispatch(new SendSmsJob($lead->phone, $template->content)); // SendSmsJob एक नया Job होगा
                Log::info("SMS '{$template->title}' queued for Lead {$lead->id}.");
            } else {
                Log::warning("SMS template {$action->sms_template_id} not found for Lead {$lead->id}.");
            }
        } else {
            Log::warning("Cannot send SMS for Lead {$lead->id}: No template ID or phone number.");
        }
    }

    private function changeLeadStage(Lead $lead, AutomationAction $action)
    {
        // यहाँ लीड का स्टेज बदलने का लॉजिक लिखें
        // $action->new_stage एक array है, आप पहला स्टेज या सभी स्टेज असाइन कर सकते हैं
        if (!empty($action->new_stage)) {
            $lead->stage = $action->new_stage[0]; // Assuming you want to set the first selected stage
            $lead->save();
            Log::info("Lead {$lead->id} stage changed to '{$action->new_stage[0]}'.");
        } else {
            Log::warning("No new stage provided for Lead {$lead->id}.");
        }
    }

    private function addLeadNote(Lead $lead, AutomationAction $action)
    {
        // यहाँ लीड में नोट जोड़ने का लॉजिक लिखें
        // आप इसे Lead model के 'notes' field में append कर सकते हैं या एक dedicated Note model बना सकते हैं।
        $lead->notes = ($lead->notes ? $lead->notes . "\n" : "") . "Action Plan Note: " . $action->note_content;
        $lead->save();
        Log::info("Note added to Lead {$lead->id}.");
    }

    private function addLeadTags(Lead $lead, AutomationAction $action)
    {
        // यहाँ लीड में टैग जोड़ने का लॉजिक लिखें
        // Assuming 'tag' is a single string field, you might need to parse/append or use a many-to-many relationship.
        // For simplicity, appending to a comma-separated string:
        if (!empty($action->add_tags)) {
            $currentTags = explode(',', $lead->tag ?? '');
            foreach ($action->add_tags as $tagId) {
                // Assuming you can map tagId to tag name
                $tagName = 'Tag' . $tagId; // Replace with actual tag name lookup
                if (!in_array($tagName, $currentTags)) {
                    $currentTags[] = $tagName;
                }
            }
            $lead->tag = implode(',', array_filter($currentTags));
            $lead->save();
            Log::info("Tags added to Lead {$lead->id}.");
        }
    }

    private function removeLeadTags(Lead $lead, AutomationAction $action)
    {
        // यहाँ लीड से टैग हटाने का लॉजिक लिखें
        if (!empty($action->remove_tags)) {
            $currentTags = explode(',', $lead->tag ?? '');
            $tagsToRemove = [];
            foreach ($action->remove_tags as $tagId) {
                $tagsToRemove[] = 'Tag' . $tagId; // Replace with actual tag name lookup
            }
            $filteredTags = array_diff($currentTags, $tagsToRemove);
            $lead->tag = implode(',', array_filter($filteredTags));
            $lead->save();
            Log::info("Tags removed from Lead {$lead->id}.");
        }
    }

    private function removeAllLeadTags(Lead $lead)
    {
        // यहाँ लीड से सभी टैग हटाने का लॉजिक लिखें
        $lead->tag = null;
        $lead->save();
        Log::info("All tags removed from Lead {$lead->id}.");
    }

    private function pauseAllOtherActionPlans(Lead $lead, $currentActionPlanId)
    {
        // इस लीड के लिए अन्य सभी एक्टिव एक्शन प्लान्स को 'paused' करें
        LeadActionPlanAssignment::where('lead_id', $lead->id)
            ->where('id', '!=', $currentActionPlanId)
            ->where('status', 'active')
            ->update(['status' => 'paused']);
        Log::info("All other action plans paused for Lead {$lead->id}.");
    }

    private function pauseSpecificActionPlan(Lead $lead, AutomationAction $action)
    {
        // किसी विशिष्ट एक्शन प्लान को 'paused' करें
        if ($action->pause_specific_plan) {
            LeadActionPlanAssignment::where('lead_id', $lead->id)
                ->where('action_plan_id', $action->pause_specific_plan)
                ->where('status', 'active')
                ->update(['status' => 'paused']);
            Log::info("Specific action plan {$action->pause_specific_plan} paused for Lead {$lead->id}.");
        }
    }

    private function assignAnotherActionPlan(Lead $lead, AutomationAction $action)
    {
        // किसी अन्य एक्शन प्लान को असाइन करें
        if ($action->assign_action_plan) {
            // यहां वही लॉजिक दोहराएं जो LeadController में assignActionPlans में है
            // या एक सर्विस क्लास का उपयोग करें
            $actionPlanToAssign = AutomationActionPlan::with('actions')->find($action->assign_action_plan);
            if ($actionPlanToAssign) {
                 $firstAction = $actionPlanToAssign->actions->sortBy('step_order')->first();
                 $nextActionDueAt = null;
                 $currentActionId = null;

                 if ($firstAction) {
                     $delayDays = $firstAction->delay_days;
                     $nextActionDueAt = now()->addDays($delayDays);
                     $currentActionId = $firstAction->id;
                 }

                LeadActionPlanAssignment::firstOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'action_plan_id' => $actionPlanToAssign->id,
                    ],
                    [
                        'status' => 'active',
                        'current_action_id' => $currentActionId,
                        'next_action_due_at' => $nextActionDueAt,
                        'assigned_at' => now(),
                    ]
                );
                Log::info("Another action plan {$actionPlanToAssign->name} assigned to Lead {$lead->id}.");
            } else {
                Log::warning("Action plan to assign {$action->assign_action_plan} not found for Lead {$lead->id}.");
            }
        }
    }
}
