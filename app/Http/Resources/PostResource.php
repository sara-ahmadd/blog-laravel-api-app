<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\LikeResource;
use App\Http\Resources\ImageResource;


class PostResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'description' => $this->description,
            'tags' => $this->tags,
            'created_at'=>$this->created_at?$this->created_at->format('Y D m - h:i A'):null,
            'updated_at'=>$this->updated_at?$this->updated_at?->format('Y D m - h:i A'):null,
            // use this to return all specific fields on user model
            //  'author' => $this->whenLoaded('author', function(){
            //     return [
            //         'id' => $this->author->id,
            //         'name' => $this->author->name,
            //         'email' => $this->author->email,
            //     ];
            // })

            // use this to return all fields defined on user resource
            'author' => new UserResource($this->whenLoaded('author')),
            'likes' => LikeResource::collection($this->whenLoaded('likes')),
            'likes_count' => $this->whenCounted('likes'),
            'images' => $this->whenLoaded(
                'images',
                fn () => ImageResource::collection($this->resource->getRelation('images'))
            ),
        ];
    }
}
