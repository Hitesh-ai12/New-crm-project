<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateFolder extends Model
{
    use HasFactory;

    // Use 'user_id' as per your migration
    protected $fillable = ['name', 'type', 'user_id'];

    protected $table = 'template_folders';

    public function templates()
    {
        return $this->hasMany(Template::class, 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
