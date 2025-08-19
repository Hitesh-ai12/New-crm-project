<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Automation\AutomationActionPlan;
use App\Models\Automation\AutomationAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Template;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AutomationController extends Controller
{
     /**
     * Adds or updates an 'Add Tag(s)' action to specified action plans.
     */
    public function addTagActionToActionPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_plan_ids' => 'required|array|min:1',
            'action_plan_ids.*' => 'integer|exists:automation_action_plans,id',
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => [
                'integer',
                Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('type', 'tag');
                }),
            ],
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
            $updatedPlansCount = 0;
            $actionPlanIds = $validated['action_plan_ids'];
            $tagIdsToAdd = $validated['tag_ids'];

            foreach ($actionPlanIds as $planId) {
                $actionPlan = AutomationActionPlan::with('actions')->find($planId);

                if (!$actionPlan) {
                    continue;
                }

                $addTagAction = $actionPlan->actions->firstWhere('type', 'Add Tag(s)');

                if ($addTagAction) {
                    $currentTags = $addTagAction->add_tags ?? [];
                    $mergedTags = array_unique(array_merge($currentTags, $tagIdsToAdd));
                    $addTagAction->add_tags = $mergedTags;
                    $addTagAction->save();
                } else {
                    $addTagAction = new AutomationAction([
                        'plan_id' => $actionPlan->id,
                        'type' => 'Add Tag(s)',
                        'delay_days' => 0,
                        'add_tags' => $tagIdsToAdd,
                        'step_order' => $actionPlan->actions->max('step_order') + 1,
                    ]);
                    $actionPlan->actions()->save($addTagAction);
                    $actionPlan->step_count = $actionPlan->step_count + 1;
                    $actionPlan->save();
                }
                $updatedPlansCount++;
            }
            DB::commit();

            return response()->json([
                'message' => "Tags action added/updated in {$updatedPlansCount} action plan(s) successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add/update tags action to action plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adds or updates a 'Change Stage' action to specified action plans.
     */
    public function addChangeStageActionToActionPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_plan_ids' => 'required|array|min:1',
            'action_plan_ids.*' => 'integer|exists:automation_action_plans,id',
            'stage_ids' => 'required|array|min:1',
            'stage_ids.*' => [
                'integer',
                Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('type', 'stage');
                }),
            ],
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
            $updatedPlansCount = 0;
            $actionPlanIds = $validated['action_plan_ids'];
            $stageIdsToAssign = $validated['stage_ids'];

            foreach ($actionPlanIds as $planId) {
                $actionPlan = AutomationActionPlan::with('actions')->find($planId);

                if (!$actionPlan) {
                    continue;
                }

                $changeStageAction = $actionPlan->actions->firstWhere('type', 'Change Stage');

                if ($changeStageAction) {
                    $changeStageAction->new_stage = array_unique(array_merge($changeStageAction->new_stage ?? [], $stageIdsToAssign));
                    $changeStageAction->save();
                } else {
                    $changeStageAction = new AutomationAction([
                        'plan_id' => $actionPlan->id,
                        'type' => 'Change Stage',
                        'delay_days' => 0,
                        'new_stage' => $stageIdsToAssign,
                        'step_order' => $actionPlan->actions->max('step_order') + 1,
                    ]);
                    $actionPlan->actions()->save($changeStageAction);
                    $actionPlan->step_count = $actionPlan->step_count + 1;
                    $actionPlan->save();
                }
                $updatedPlansCount++;
            }
            DB::commit();

            return response()->json([
                'message' => "Change Stage action added/updated in {$updatedPlansCount} action plan(s) successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add/update change stage action to action plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adds or updates an 'Assign Source' action to specified action plans.
     */
    public function addAssignSourceActionToActionPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action_plan_ids' => 'required|array|min:1',
            'action_plan_ids.*' => 'integer|exists:automation_action_plans,id',
            'source_ids' => 'required|array|min:1',
            'source_ids.*' => [
                'integer',
                Rule::exists('items', 'id')->where(function ($query) {
                    $query->where('type', 'source');
                }),
            ],
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
            $updatedPlansCount = 0;
            $actionPlanIds = $validated['action_plan_ids'];
            $sourceIdsToAssign = $validated['source_ids'];

            foreach ($actionPlanIds as $planId) {
                $actionPlan = AutomationActionPlan::with('actions')->find($planId);

                if (!$actionPlan) {
                    continue;
                }

                $assignSourceAction = $actionPlan->actions->firstWhere('type', 'Assign Source');

                if ($assignSourceAction) {
                    $assignSourceAction->assign_action_plan = (string)$sourceIdsToAssign[0]; 
                    $assignSourceAction->save();
                } else {
                    $assignSourceAction = new AutomationAction([
                        'plan_id' => $actionPlan->id,
                        'type' => 'Assign Source',
                        'delay_days' => 0,
                        'assign_action_plan' => (string)$sourceIdsToAssign[0], 
                        'step_order' => $actionPlan->actions->max('step_order') + 1,
                    ]);
                    $actionPlan->actions()->save($assignSourceAction);
                    $actionPlan->step_count = $actionPlan->step_count + 1;
                    $actionPlan->save();
                }
                $updatedPlansCount++;
            }
            DB::commit();

            return response()->json([
                'message' => "Assign Source action added/updated in {$updatedPlansCount} action plan(s) successfully.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add/update assign source action to action plans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   /**
     * Display a listing of the automation action plans.
     */
    public function indexActionPlans(Request $request)
    {
        $userId = Auth::id();

        $perPage = $request->input('per_page', 10);
        $searchQuery = $request->input('search');

        $query = AutomationActionPlan::with(['actions', 'creator:id,name']);

        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        $actionPlans = $query->paginate($perPage);

        $actionPlans->getCollection()->each(function ($actionPlan) use ($userId) {

            $assignments = $actionPlan->leadActionPlanAssignments()
                                    ->whereHas('lead', function ($query) use ($userId) {
                                        $query->where('user_id', $userId);
                                    })
                                    ->get();

            $statusCounts = $assignments->groupBy('status')->map->count();

            $actionPlan->active_leads_count = $statusCounts->get('active', 0);
            $actionPlan->paused_leads_count = $statusCounts->get('paused', 0);
            $actionPlan->stopped_leads_count = $statusCounts->get('stopped', 0);
            $actionPlan->completed_leads_count = $statusCounts->get('completed', 0);
        });

        return response()->json($actionPlans);
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
            
            'actions.*.email_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'email');
                }),
            ],
            'actions.*.sms_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'sms');
                }),
            ],

            'actions.*.note_content' => 'required_if:actions.*.type,Add Note|nullable|string',

            'actions.*.new_stage' => 'nullable|array',
            'actions.*.new_stage.*' => 'integer',

            'actions.*.add_tags' => 'nullable|array',
            'actions.*.add_tags.*' => 'integer',

            'actions.*.remove_tags' => 'nullable|array',
            'actions.*.remove_tags.*' => 'integer',

            'actions.*.pause_specific_plan' => 'required_if:actions.*.type,Pause Specific Action plan|nullable|integer|exists:automation_action_plans,id',
            'actions.*.assign_action_plan' => 'required_if:actions.*.type,Assign Action Plan|nullable|integer|exists:automation_action_plans,id', // <-- FIX IS HERE: Changed 'string' to 'integer'
            
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
                'created_by' => Auth::id(),
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
            
            'actions.*.email_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'email');
                }),
            ],
            'actions.*.sms_template_id' => [
                'nullable',
                'integer',
                Rule::exists('templates', 'id')->where(function ($query) {
                    $query->where('type', 'sms');
                }),
            ],

            'actions.*.note_content' => 'required_if:actions.*.type,Add Note|nullable|string',

            'actions.*.new_stage' => 'nullable|array',
            'actions.*.new_stage.*' => 'integer',

            'actions.*.add_tags' => 'nullable|array',
            'actions.*.add_tags.*' => 'integer',

            'actions.*.remove_tags' => 'nullable|array',
            'actions.*.remove_tags.*' => 'integer',

            'actions.*.pause_specific_plan' => 'required_if:actions.*.type,Pause Specific Action plan|nullable|integer|exists:automation_action_plans,id',
            'actions.*.assign_action_plan' => 'required_if:actions.*.type,Assign Action Plan|nullable|integer|exists:automation_action_plans,id', // <-- FIX IS HERE: Changed 'string' to 'integer'
            
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
