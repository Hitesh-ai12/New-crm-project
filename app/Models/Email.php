<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'from',
        'to',
        'subject',
        'message',
        'direction',
        'sent_at',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'sent_at' => 'datetime',
    ];
    
    public function lead() {
        return $this->belongsTo(Lead::class);
    }
}

