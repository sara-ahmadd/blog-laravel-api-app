<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Image;
use App\Models\Like;
use App\Models\Comment;



class Post extends Model
{
    use SoftDeletes;
    protected $fillable=[
        'title', 'body', 'author_id', 'description', 'tags'
    ];
    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }
    public function images(){
        return $this->hasMany(Image::class, 'post_id');
    }
    public function likes(){
        return $this->hasMany(Like::class, 'post_id');
    }
    public function comments(){
        return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id');
    }
    protected function casts(): array {
        return [
            'tags' => 'array'
        ];
    }
    
}
