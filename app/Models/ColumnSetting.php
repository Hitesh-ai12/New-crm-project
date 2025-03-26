<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColumnSetting extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'selected_columns'];

    protected $casts = [
        'selected_columns' => 'array', 
    ];
}
