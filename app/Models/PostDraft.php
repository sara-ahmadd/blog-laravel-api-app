<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PostDraft extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'body',
        'description',
        'tags',
        'image',
        'image_alt'  
    ];
    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }
    protected function casts(): array
        {
            return [
                'tags' => 'array',
            ];
        }
}
