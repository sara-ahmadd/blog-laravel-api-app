<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Like extends Model
{
    use SoftDeletes, HasUuids;
    protected $fillable=[
        'post_id', 'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function post(){
        return $this->belongsTo(Post::class, 'post_id');
    }
    
}
