<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadActionPlanAssignment;
use App\Models\Automation\AutomationAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LeadActionPlanAssignmentController extends Controller
{
    /**
     * Display a listing of lead action plan assignments.
     * यह सभी असाइनमेंट्स को दिखाता है, जिनका स्टेटस, लीड, और एक्शन प्लान के साथ।
     */
    public function index()
    {
        // असाइनमेंट्स को उनके संबंधित लीड, एक्शन प्लान, और वर्तमान एक्शन के साथ फेच करें।
        $assignments = LeadActionPlanAssignment::with(['lead:id,first_name,last_name', 'actionPlan:id,name', 'currentAction:id,type,task_name,note_content'])
                                                ->orderBy('assigned_at', 'desc')
                                                ->get();

        return response()->json([
            'message' => 'Lead action plan assignments fetched successfully.',
            'data' => $assignments,
        ]);
    }

    /**
     * Update the status of a specific lead action plan assignment.
     * यह किसी असाइनमेंट को पॉज़ या रिज़्यूम करने के लिए उपयोग होगा।
     */
    public function updateStatus(Request $request, LeadActionPlanAssignment $assignment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,paused,completed,stopped', // मान्य स्टेटस
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $newStatus = $request->input('status');

        DB::beginTransaction();
        try {
            $assignment->status = $newStatus;
            
            // अगर पॉज़ किया गया है, तो next_action_due_at को null कर दें ताकि शेड्यूलर इसे प्रोसेस न करे।
            // अगर एक्टिव किया गया है, तो next_action_due_at को फिर से सेट करें (अगर यह null है और current_action_id है)।
            if ($newStatus === 'paused') {
                $assignment->next_action_due_at = null;
            } elseif ($newStatus === 'active' && $assignment->current_action_id && !$assignment->next_action_due_at) {
                // अगर असाइनमेंट एक्टिव हो रहा है और कोई करंट एक्शन है लेकिन ड्यू डेट नहीं है,
                // तो उसे तुरंत प्रोसेस करने के लिए ड्यू डेट सेट करें।
                $assignment->next_action_due_at = now();
            }

            $assignment->save();
            DB::commit();

            return response()->json([
                'message' => "Assignment status updated to '{$newStatus}' successfully.",
                'data' => $assignment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update assignment status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
