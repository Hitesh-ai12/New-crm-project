<?php

namespace App\Models\Automation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationLeadTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'plan_id',
        'status', // active, paused, completed, etc.
    ];

    public function plan()
    {
        return $this->belongsTo(AutomationActionPlan::class, 'plan_id');
    }
}
