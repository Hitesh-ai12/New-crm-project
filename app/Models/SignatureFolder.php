<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SignatureTemplate;
use App\Models\User;

class SignatureFolder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'user_id'];

    /**
     * Folder created by this user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * All templates under this folder
     */
    public function templates()
    {
        return $this->hasMany(SignatureTemplate::class, 'folder_id');
    }
}
