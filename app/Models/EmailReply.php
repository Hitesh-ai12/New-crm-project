<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lead_id', 'from', 'to', 'subject', 'message', 'received_at',
    ];
    
    protected $casts = [
    'attachments' => 'array',
    'received_at' => 'datetime',
];
}
