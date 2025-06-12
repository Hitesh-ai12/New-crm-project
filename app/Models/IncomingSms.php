<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingSms extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'from',
        'to',
        'message',
        'received_at',
    ];
}
