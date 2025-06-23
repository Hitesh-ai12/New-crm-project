<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    use HasFactory;

    protected $table = 'sms_messages'; // Optional if table name differs

    protected $fillable = [
        'user_id',
        'lead_id',
        'from',
        'to',
        'message',
        'type',               // 'sent' or 'received'
        'delivery_status',    // 'pending', 'sent', 'delivered', 'failed'
        'reply_to_id',        // reference to another SMS message
        'webhook_payload',    // JSON from webhook if applicable
        'timestamp',          // actual send/receive time
    ];

    protected $casts = [
        'webhook_payload' => 'array',
        'timestamp'       => 'datetime',
    ];

    // Relationships (optional but useful for chaining)
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(SmsMessage::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(SmsMessage::class, 'reply_to_id');
    }
}
