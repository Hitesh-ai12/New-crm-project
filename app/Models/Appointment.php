<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

        protected $fillable = [
            'lead_id',
            'title',
            'description',
            'location',
            'date',
            'user_id',
        ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
