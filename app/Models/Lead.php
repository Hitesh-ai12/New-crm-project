<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name (which it isn't in your case)
    protected $table = 'service_leads';

    // Specify which attributes can be mass-assigned
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'tag', 'stage', 'source', 'activity'];

    // If needed, you can also define the attributes that should be cast to native types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
