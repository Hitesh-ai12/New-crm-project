<?php

namespace App\Models\Automation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'type',
        'delay_days',
        'task_name',
        'task_type',
        'email_template_id',
        'sms_template_id',
        'note_content',
        'new_stage',
        'add_tags',
        'remove_tags',
        'pause_specific_plan',
        'assign_action_plan',
        'step_order',
    ];

    protected $casts = [
        'new_stage' => 'array',
        'add_tags' => 'array',
        'remove_tags' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(AutomationActionPlan::class, 'plan_id');
    }
}
