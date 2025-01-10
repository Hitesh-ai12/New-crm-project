<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    // Define the table name if it's not following Laravel's naming convention
    protected $table = 'api_keys';

    // Define which attributes should be mass-assignable
    protected $fillable = [
        'user_id', 'key', 'endpoint', 'permissions',
    ];

    // Cast the permissions attribute as an array for proper handling
    protected $casts = [
        'permissions' => 'array',
    ];

    // Ensure the created_at and updated_at timestamps are managed automatically
    public $timestamps = true;  // This is default, but it's good practice to explicitly define it.

    // Optionally, you can hide sensitive data, like the API key itself, when returning the model
    protected $hidden = [
        'key',  // Hide API key when converting the model to JSON
    ];
}
