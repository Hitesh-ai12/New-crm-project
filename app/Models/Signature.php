<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;
    // Define fillable attributes to allow mass assignment
    protected $fillable = [
        'name',
        'content',
        'is_default',
    ];

}
