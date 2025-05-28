<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'subject',
        'content',
        'attachment_path',
        'created_by',
        'folder_id', // ✅ Added folder_id to allow mass assignment
    ];

    // Creator relationship
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Folder relationship
    public function folder()
    {
        return $this->belongsTo(TemplateFolder::class, 'folder_id');
    }
}
