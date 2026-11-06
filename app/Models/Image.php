<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Post;

class Image extends Model
{
    use HasUuids;
    protected $fillable = [
        'post_id',
        'image_path'
    ];

    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
}
