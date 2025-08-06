<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeadActionPlanAssignment;
use App\Models\Automation\AutomationAction;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComposeEmail;
use App\Mail\LeadMail;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mail\CrmMailable;
use App\Models\Email;
use Twilio\Rest\Client;
use App\Models\SmsMessage;

class ProcessLeadActionPlans extends Command
{
    /**
     * The name and signature of the console command.
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
        Log::channel('cron_jobs')->info('Starting to process lead action plans.');
        $this->info('Starting to process lead action plans.');

        $pendingAssignments = LeadActionPlanAssignment::where('status', 'active')
            ->whereNotNull('next_action_due_at')
            ->where('next_action_due_at', '<=', now())
            ->with(['lead', 'actionPlan.actions'])
            ->get();

        $this->info("Found " . $pendingAssignments->count() . " pending assignments.");
        Log::channel('cron_jobs')->info("Found " . $pendingAssignments->count() . " pending assignments.");

        foreach ($pendingAssignments as $assignment) {
            DB::beginTransaction();
            try {
                if (!$assignment->lead || !$assignment->actionPlan) {
                    Log::channel('cron_jobs')->warning("Skipping assignment {$assignment->id}: Lead or Action Plan not found.");
                    $assignment->status = 'stopped';
                    $assignment->save();
                    DB::commit();
                    continue;
                }

                $currentAction = $assignment->actionPlan->actions
                    ->firstWhere('id', $assignment->current_action_id ?? $assignment->actionPlan->actions->sortBy('step_order')->first()->id);
                
                if (!$currentAction) {
                    Log::channel('cron_jobs')->warning("Skipping assignment {$assignment->id}: No current action found or plan has no actions. Setting status to completed.");
                    $assignment->status = 'completed';
                    $assignment->save();
                    DB::commit();
                    continue;
                }

                $this->info("Processing action '{$currentAction->type}' for Lead '{$assignment->lead->first_name}' from Plan '{$assignment->actionPlan->name}'");
                Log::channel('cron_jobs')->info("Processing action '{$currentAction->type}' for Lead '{$assignment->lead->first_name}' from Plan '{$assignment->actionPlan->name}'.");

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
                        Log::channel('cron_jobs')->info("Repeat Action Plan not yet implemented for assignment {$assignment->id}.");
                        break;
                    case 'Assign Action Plan':
                        $this->assignAnotherActionPlan($assignment->lead, $currentAction);
                        break;
                    default:
                        Log::channel('cron_jobs')->warning("Unknown action type '{$currentAction->type}' for assignment {$assignment->id}.");
                        break;
                }

                $nextAction = $assignment->actionPlan->actions
                    ->where('step_order', '>', $currentAction->step_order)
                    ->sortBy('step_order')
                    ->first();

                if ($nextAction) {
                    $assignment->current_action_id = $nextAction->id;
                    $assignment->next_action_due_at = now()->addDays($nextAction->delay_days);
                    $this->info("Next action for assignment {$assignment->id} is '{$nextAction->type}' due at {$assignment->next_action_due_at}.");
                    Log::channel('cron_jobs')->info("Next action for assignment {$assignment->id} is '{$nextAction->type}' due at {$assignment->next_action_due_at}.");
                } else {
                    $assignment->status = 'completed';
                    $assignment->current_action_id = null;
                    $assignment->next_action_due_at = null;
                    $this->info("Action plan {$assignment->actionPlan->name} completed for Lead {$assignment->lead->first_name}.");
                    Log::channel('cron_jobs')->info("Action plan {$assignment->actionPlan->name} completed for Lead {$assignment->lead->first_name}.");
                }

                $assignment->save();
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('cron_jobs')->error("Error processing assignment {$assignment->id}: " . $e->getMessage());
                $assignment->status = 'stopped';
                $assignment->save();
                $this->error("Error processing assignment {$assignment->id}: " . $e->getMessage());
            }
        }

        Log::channel('cron_jobs')->info('Finished processing lead action plans.');
        $this->info('Action plan processing finished.');
    }

    private function createTask(Lead $lead, AutomationAction $action)
    {
        $lead->addJsonTask([
            'name' => $action->task_name,
            'type' => $action->task_type,
            'status' => 'pending',
            'created_at' => now()->toDateTimeString(),
        ]);
        Log::channel('cron_jobs')->info("Task '{$action->task_name}' created for Lead {$lead->id}.");
    }

    private function sendEmail(Lead $lead, AutomationAction $action)
    {
        if ($action->email_template_id && $lead->email) {
            $template = Template::find($action->email_template_id);
            if ($template) {
                $placeholders = [
                    '{{first_name}}' => $lead->first_name,
                    '{{last_name}}' => $lead->last_name ?? '',
                    '{{email}}' => $lead->email,
                    '{{phone}}' => $lead->phone ?? '',
                    '{{city}}' => $lead->city ?? '',
                ];

                $personalizedSubject = strtr($template->subject, $placeholders);
                $personalizedMessage = strtr($template->content, $placeholders);
                $from = config('mail.from.address');
                $userId = $lead->user_id;

                try {
                    Mail::to($lead->email)->queue(new CrmMailable([
                        'from' => $from,
                        'to' => $lead->email,
                        'subject' => $personalizedSubject,
                        'message' => $personalizedMessage,
                        'attachments' => [],
                        'attachmentPaths' => [],
                    ]));

                    Email::create([
                        'lead_id' => $lead->id,
                        'user_id' => $userId,
                        'to' => $lead->email,
                        'from' => $from,
                        'subject' => $personalizedSubject,
                        'message' => $personalizedMessage,
                        'direction' => 'sent',
                        'sent_at' => now(),
                        'attachments' => json_encode([]),
                    ]);

                    Log::channel('cron_jobs')->info("Email '{$template->title}' queued for Lead {$lead->id}.");
                } catch (\Exception $e) {
                    Log::channel('cron_jobs')->error("Failed to send email to {$lead->email}: " . $e->getMessage());
                }
            } else {
                Log::channel('cron_jobs')->warning("Email template {$action->email_template_id} not found for Lead {$lead->id}.");
            }
        } else {
            Log::channel('cron_jobs')->warning("Cannot send email for Lead {$lead->id}: No template ID or email address.");
        }
    }

    private function sendText(Lead $lead, AutomationAction $action)
    {
        if ($action->sms_template_id && $lead->phone) {
            $template = Template::find($action->sms_template_id);
            if ($template) {
                $twilioSid = config('services.twilio.sid');
                $twilioAuthToken = config('services.twilio.token');
                $twilioPhoneNumber = config('services.twilio.phone_number');

                if (!$twilioSid || !$twilioAuthToken || !$twilioPhoneNumber) {
                    Log::channel('cron_jobs')->error('Twilio credentials are missing!');
                    return;
                }

                $twilio = new Client($twilioSid, $twilioAuthToken);
                
                $placeholders = [
                    '{{first_name}}' => $lead->first_name,
                    '{{last_name}}' => $lead->last_name ?? '',
                    '{{email}}' => $lead->email ?? '',
                    '{{phone}}' => $lead->phone ?? '',
                    '{{city}}' => $lead->city ?? '',
                ];

                $personalizedMessage = strtr($template->content, $placeholders);

                try {
                    $twilio->messages->create($lead->phone, [
                        'from' => $twilioPhoneNumber,
                        'body' => $personalizedMessage,
                    ]);

                    SmsMessage::create([
                        'user_id' => $lead->user_id,
                        'lead_id' => $lead->id,
                        'from' => $twilioPhoneNumber,
                        'to' => $lead->phone,
                        'message' => $personalizedMessage,
                        'type' => 'sent',
                        'delivery_status' => 'sent',
                        'timestamp' => now(),
                    ]);

                    Log::channel('cron_jobs')->info("SMS '{$template->title}' sent to Lead {$lead->id}.");
                } catch (\Exception $e) {
                    Log::channel('cron_jobs')->error("Failed to send SMS to {$lead->phone}: " . $e->getMessage());
                }
            } else {
                Log::channel('cron_jobs')->warning("SMS template {$action->sms_template_id} not found for Lead {$lead->id}.");
            }
        } else {
            Log::channel('cron_jobs')->warning("Cannot send SMS for Lead {$lead->id}: No template ID or phone number.");
        }
    }

    private function changeLeadStage(Lead $lead, AutomationAction $action)
    {
        if (!empty($action->new_stage)) {
            $lead->stage = $action->new_stage[0];
            $lead->save();
            Log::channel('cron_jobs')->info("Lead {$lead->id} stage changed to '{$action->new_stage[0]}'.");
        } else {
            Log::channel('cron_jobs')->warning("No new stage provided for Lead {$lead->id}.");
        }
    }

    private function addLeadNote(Lead $lead, AutomationAction $action)
    {
        // Assuming 'notes' field is a string, we append to it.
        $lead->notes = ($lead->notes ? $lead->notes . "\n" : "") . "Note: " . $action->note_content;
        $lead->save();
        Log::channel('cron_jobs')->info("Note added to Lead {$lead->id}.");
    }

    private function addLeadTags(Lead $lead, AutomationAction $action)
    {
        if (!empty($action->add_tags)) {
            $currentTags = explode(',', $lead->tag ?? '');
            
            // Map tag IDs to tag names. You might need to fetch this from a 'tags' table.
            foreach ($action->add_tags as $tagId) {
                // For demonstration, let's assume Tag Model exists
                // $tag = \App\Models\Tag::find($tagId);
                // $tagName = $tag ? $tag->name : 'Tag' . $tagId;
                $tagName = 'Tag' . $tagId; 
                if (!in_array($tagName, $currentTags)) {
                    $currentTags[] = $tagName;
                }
            }
            $lead->tag = implode(',', array_filter($currentTags));
            $lead->save();
            Log::channel('cron_jobs')->info("Tags added to Lead {$lead->id}.");
        }
    }

    private function removeLeadTags(Lead $lead, AutomationAction $action)
    {
        if (!empty($action->remove_tags)) {
            $currentTags = explode(',', $lead->tag ?? '');
            $tagsToRemove = [];
            foreach ($action->remove_tags as $tagId) {
                // Assuming you can map tagId to tag name
                $tagsToRemove[] = 'Tag' . $tagId; // Placeholder logic
            }
            $filteredTags = array_diff($currentTags, $tagsToRemove);
            $lead->tag = implode(',', array_filter($filteredTags));
            $lead->save();
            Log::channel('cron_jobs')->info("Tags removed from Lead {$lead->id}.");
        }
    }

    private function removeAllLeadTags(Lead $lead)
    {
        $lead->tag = null;
        $lead->save();
        Log::channel('cron_jobs')->info("All tags removed from Lead {$lead->id}.");
    }

    private function pauseAllOtherActionPlans(Lead $lead, $currentActionPlanId)
    {
        LeadActionPlanAssignment::where('lead_id', $lead->id)
            ->where('action_plan_id', '!=', $currentActionPlanId) 
            ->where('status', 'active')
            ->update(['status' => 'paused']);
        Log::channel('cron_jobs')->info("All other action plans paused for Lead {$lead->id}.");
    }

    private function pauseSpecificActionPlan(Lead $lead, AutomationAction $action)
    {
        if ($action->pause_specific_plan) {
            LeadActionPlanAssignment::where('lead_id', $lead->id)
                ->where('action_plan_id', $action->pause_specific_plan)
                ->where('status', 'active')
                ->update(['status' => 'paused']);
            Log::channel('cron_jobs')->info("Specific action plan {$action->pause_specific_plan} paused for Lead {$lead->id}.");
        }
    }
    
    private function assignAnotherActionPlan(Lead $lead, AutomationAction $action)
    {
        if ($action->assign_action_plan) {
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
                Log::channel('cron_jobs')->info("Another action plan {$actionPlanToAssign->name} assigned to Lead {$lead->id}.");
            } else {
                Log::channel('cron_jobs')->warning("Action plan to assign {$action->assign_action_plan} not found for Lead {$lead->id}.");
            }
        }
    }
}
