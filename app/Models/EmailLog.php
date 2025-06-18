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
            'message', // or 'body' if your column is named that
            'sent_at',
            'lead_id',
            'user_id',
            'direction',
            'attachments',
        ];

    protected $dates = ['sent_at'];
}
