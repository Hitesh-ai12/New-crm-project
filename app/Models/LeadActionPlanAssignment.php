<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Automation\AutomationActionPlan;
use App\Models\Automation\AutomationAction; // ✅ ADD THIS LINE
use App\Models\Lead;

class LeadActionPlanAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'action_plan_id',
        'status',
        'current_action_id',
        'next_action_due_at',
        'assigned_at',
    ];

    protected $casts = [
        'next_action_due_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function actionPlan()
    {
        return $this->belongsTo(AutomationActionPlan::class, 'action_plan_id');
    }

    public function currentAction()
    {
        return $this->belongsTo(AutomationAction::class, 'current_action_id');
    }
}
