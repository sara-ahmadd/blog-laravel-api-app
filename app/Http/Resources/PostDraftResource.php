<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;


class PostDraftResource extends JsonResource
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
            'image_alt' => $this->image_alt,
            'description' => $this->description,
            'image_url' => $this->image ? asset('storage/'.$this->image):null,
            'tags' => $this->tags,
            'created_at'=>$this->created_at->format('Y D m - h:i A'),
            'updated_at'=>$this->updated_at->format('Y D m - h:i A'),
            // use this to return all specific fields on user model
            //  'author' => $this->whenLoaded('author', function(){
            //     return [
            //         'id' => $this->author->id,
            //         'name' => $this->author->name,
            //         'email' => $this->author->email,
            //     ];
            // })

            // use this to return all fields dfined on user resource
        'author' => new UserResource($this->whenLoaded('author'))];
    }
}
