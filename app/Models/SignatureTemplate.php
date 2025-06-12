<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SignatureFolder;
use App\Models\User;

class SignatureTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'folder_id',
        'type',
        'content',
        'attachment_path',
        'user_id'
    ];

    /**
     * The folder this template belongs to
     */
    public function folder()
    {
        return $this->belongsTo(SignatureFolder::class, 'folder_id');
    }

    /**
     * The user who created the template
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
