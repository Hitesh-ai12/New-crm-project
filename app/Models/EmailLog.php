<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'to',
        'from',
        'subject',
        'body',
        'sent_at',
    ];

    protected $dates = ['sent_at'];
}
