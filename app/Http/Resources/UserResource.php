<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\LikeResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_pic' => $this->profile_pic ? asset('storage/'.$this->profile_pic):null,
            'created_at'=>$this->created_at->format('Y D m - h:i A'),
            'updated_at'=>$this->updated_at->format('Y D m - h:i A'),
            'posts' => $this->whenLoaded('posts', fn()=>$this->posts->pluck('id')),
           
            'likes' =>$this->whenLoaded('likes', fn () => LikeResource::collection($this->likes)),

            'liked_posts' => $this->whenLoaded(
                    'likedPosts',
                    fn () => PostResource::collection($this->likedPosts)
                ),
            'likes_count' => $this->whenCounted('likes'),
        ];
    }
}
