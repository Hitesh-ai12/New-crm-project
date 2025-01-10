<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'leads';

    // Specify which attributes can be mass-assigned
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'user_id',  
        'new_listing_alert',
        'lead_basic_details',
        'neighbourhood_alert',
        'open_house_alert',
        'price_drop_alert',
        'action_plans',
        'files',
        'background',
        'real_estate_newsletter',
        'market_updates',
        'city',
        'tasks',
        'appointments',
    ];

    // Cast attributes to native types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tasks' => 'array', // Assumes tasks is stored as JSON
        'appointments' => 'array', // Assumes appointments is stored as JSON
    ];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
