<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Automation\AutomationActionPlan;
use App\Models\Automation\AutomationAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Template; // Template model import karein


class AutomationController extends Controller
{
    /**
     * Display a listing of the automation action plans.
     */
    public function indexActionPlans(Request $request)
    {
        $actionPlans = AutomationActionPlan::with(['actions', 'creator:id,name'])->get();

        return response()->json([
            'message' => 'Action plans fetched successfully.',
            'data' => $actionPlans,
        ]);
    }

     /**
     * Store a newly created automation action plan.
     */
    public function storeActionPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'pause_on_reply' => 'boolean',
            'actions' => 'required|array|min:1',
            'actions.*.type' => 'required|string|in:Create Task,Send Email,Send Text,Change Stage,Add Note,Add Tag(s),Remove Tag(s),Remove all Tags,Pause all other Action plans,Pause Specific Action plan,Repeat Action Plan,Assign Action Plan',
            'actions.*.delay_days' => 'required|numeric|min:0',

            'actions.*.task_name' => 'required_if:actions.*.type,Create Task|nullable|string|max:255',
            'actions.*.task_type' => 'required_if:actions.*.type,Create Task|nullable|string|max:255',
            
            // --- CHANGES START HERE (storeActionPlan) ---
            // email_template_id should exist in 'templates' table with type 'email'
            'actions.*.email_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'email');
                }),
            ],
            // sms_template_id should exist in 'templates' table with type 'sms'
            'actions.*.sms_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'sms');
                }),
            ],
            // --- CHANGES END HERE ---

            'actions.*.note_content' => 'required_if:actions.*.type,Add Note|nullable|string',

            'actions.*.new_stage' => 'nullable|array',
            'actions.*.new_stage.*' => 'integer',

            'actions.*.add_tags' => 'nullable|array',
            'actions.*.add_tags.*' => 'integer',

            'actions.*.remove_tags' => 'nullable|array',
            'actions.*.remove_tags.*' => 'integer',

            'actions.*.pause_specific_plan' => 'required_if:actions.*.type,Pause Specific Action plan|nullable|string|exists:automation_action_plans,id',
            'actions.*.assign_action_plan' => 'required_if:actions.*.type,Assign Action Plan|nullable|string|exists:automation_action_plans,id',
            
            'actions.*.step_order' => 'required|integer|min:0',
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
            $plan = AutomationActionPlan::create([
                'name' => $validated['title'],
                'pause_on_reply' => $validated['pause_on_reply'] ?? false,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['actions'] as $index => $actionData) {
                $dataToSave = array_filter([
                    'plan_id' => $plan->id,
                    'type' => $actionData['type'],
                    'delay_days' => $actionData['delay_days'],
                    'task_name' => $actionData['task_name'] ?? null,
                    'task_type' => $actionData['task_type'] ?? null,
                    'email_template_id' => $actionData['email_template_id'] ?? null,
                    'sms_template_id' => $actionData['sms_template_id'] ?? null,
                    'note_content' => $actionData['note_content'] ?? null,
                    'new_stage' => $actionData['new_stage'] ?? null,
                    'add_tags' => $actionData['add_tags'] ?? null,
                    'remove_tags' => $actionData['remove_tags'] ?? null,
                    'pause_specific_plan' => $actionData['pause_specific_plan'] ?? null,
                    'assign_action_plan' => $actionData['assign_action_plan'] ?? null,
                    'step_order' => $actionData['step_order'],
                ], function($value) {
                    if (is_array($value)) return true;
                    return $value !== null;
                });
                
                AutomationAction::create($dataToSave);
            }

            $plan->step_count = count($validated['actions']);
            $plan->save();

            DB::commit();

            return response()->json([
                'message' => 'Action Plan created successfully.',
                'data' => $plan->load(['actions', 'creator']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create action plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified automation action plan.
     */
    public function showActionPlan(Request $request, AutomationActionPlan $actionPlan)
    {
        if ($request->query('include') === 'actions') {
            $actionPlan->load('actions');
        }

        return response()->json([
            'message' => 'Action plan fetched successfully.',
            'data' => $actionPlan,
        ]);
    }

     /**
     * Update the specified automation action plan in storage.
     */
    public function updateActionPlan(Request $request, AutomationActionPlan $actionPlan)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'pause_on_reply' => 'boolean',
            'actions' => 'required|array|min:1',
            'actions.*.id' => 'nullable|integer|exists:automation_actions,id',
            'actions.*.type' => 'required|string|in:Create Task,Send Email,Send Text,Change Stage,Add Note,Add Tag(s),Remove Tag(s),Remove all Tags,Pause all other Action plans,Pause Specific Action plan,Repeat Action Plan,Assign Action Plan',
            'actions.*.delay_days' => 'required|numeric|min:0',

            'actions.*.task_name' => 'required_if:actions.*.type,Create Task|nullable|string|max:255',
            'actions.*.task_type' => 'required_if:actions.*.type,Create Task|nullable|string|max:255',
            
            // --- CHANGES START HERE (updateActionPlan) ---
            // email_template_id should exist in 'templates' table with type 'email'
            'actions.*.email_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'email');
                }),
            ],
            // sms_template_id should exist in 'templates' table with type 'sms'
            'actions.*.sms_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'sms');
                }),
            ],
            // --- CHANGES END HERE ---

            'actions.*.note_content' => 'required_if:actions.*.type,Add Note|nullable|string',

            'actions.*.new_stage' => 'nullable|array',
            'actions.*.new_stage.*' => 'integer',

            'actions.*.add_tags' => 'nullable|array',
            'actions.*.add_tags.*' => 'integer',

            'actions.*.remove_tags' => 'nullable|array',
            'actions.*.remove_tags.*' => 'integer',

            'actions.*.pause_specific_plan' => 'required_if:actions.*.type,Pause Specific Action plan|nullable|string|exists:automation_action_plans,id',
            'actions.*.assign_action_plan' => 'required_if:actions.*.type,Assign Action Plan|nullable|string|exists:automation_action_plans,id',
            
            'actions.*.step_order' => 'required|integer|min:0',
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
            $actionPlan->update([
                'name' => $validated['title'],
                'pause_on_reply' => $validated['pause_on_reply'] ?? false,
                'step_count' => count($validated['actions']),
            ]);

            $existingActionIds = $actionPlan->actions->pluck('id')->toArray();
            $incomingActionIds = collect($validated['actions'])
                                    ->pluck('id')
                                    ->filter()
                                    ->toArray();

            $actionsToDelete = array_diff($existingActionIds, $incomingActionIds);
            if (!empty($actionsToDelete)) {
                AutomationAction::whereIn('id', $actionsToDelete)->delete();
            }

            foreach ($validated['actions'] as $index => $actionData) {
                $dataToSave = array_filter([
                    'plan_id' => $actionPlan->id,
                    'type' => $actionData['type'],
                    'delay_days' => $actionData['delay_days'],
                    'task_name' => $actionData['task_name'] ?? null,
                    'task_type' => $actionData['task_type'] ?? null,
                    'email_template_id' => $actionData['email_template_id'] ?? null,
                    'sms_template_id' => $actionData['sms_template_id'] ?? null,
                    'note_content' => $actionData['note_content'] ?? null,
                    'new_stage' => $actionData['new_stage'] ?? null,
                    'add_tags' => $actionData['add_tags'] ?? null,
                    'remove_tags' => $actionData['remove_tags'] ?? null,
                    'pause_specific_plan' => $actionData['pause_specific_plan'] ?? null,
                    'assign_action_plan' => $actionData['assign_action_plan'] ?? null,
                    'step_order' => $actionData['step_order'],
                ], function($value) {
                    if (is_array($value)) return true;
                    return $value !== null;
                });

                if (isset($actionData['id']) && $actionData['id']) {
                    AutomationAction::where('id', $actionData['id'])
                                    ->where('plan_id', $actionPlan->id)
                                    ->update($dataToSave);
                } else {
                    AutomationAction::create($dataToSave);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Action Plan updated successfully.',
                'data' => $actionPlan->load(['actions', 'creator']),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update action plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   /**
     * Delete the specified automation action plan(s) from storage.
     */
    public function batchDeleteActionPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:automation_action_plans,id',
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
            $deletedCount = AutomationActionPlan::whereIn('id', $validated['ids'])->delete();

            DB::commit();

            return response()->json([
                'message' => "{$deletedCount} Action Plan(s) deleted successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete action plan(s).',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
