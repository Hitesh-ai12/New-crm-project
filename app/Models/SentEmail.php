<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    use HasFactory;
   protected $fillable = [
        'user_id',
        'lead_id',
        'from',
        'to',
        'subject',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
