<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

        protected $table = 'leads';

        protected $fillable = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'user_id',  
            'tag',
            'stage',
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
        
        protected $casts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'tasks' => 'array', 
            'appointments' => 'array', 
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }
}
