<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Comment extends Model
{
    use HasUuids;
    protected $fillable = [
        'user_id', 'post_id', 'replies', 'content','parent_id'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
    public function replies(){
        return $this->hasMany(Comment::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
