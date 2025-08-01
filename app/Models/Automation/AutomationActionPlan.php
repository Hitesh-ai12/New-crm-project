<?php

namespace App\Models\Automation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AutomationActionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pause_on_reply', // <--- ADD THIS LINE
        'step_count',
        'created_by',
        'tracking_stats',
    ];

    protected $casts = [
        'tracking_stats' => 'array',
        'pause_on_reply' => 'boolean',
    ];

    public function actions()
    {
        return $this->hasMany(AutomationAction::class, 'plan_id')->orderBy('step_order');
    }

    /**
     * Get the user who created the action plan.
     */
    public function creator()
    {
        // Yahan hum 'created_by' foreign key ko 'users' table ke 'id' se jod rahe hain.
        return $this->belongsTo(User::class, 'created_by');
    }
}
