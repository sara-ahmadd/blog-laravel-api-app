<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\PostRepoInterface;
use App\Models\Post;


class PostRepo implements PostRepoInterface{
      public function exists(array $conditions):bool{
        return Post::where($conditions)->exists();
    }
    public function getAll(array $filters, array $relations){
        $query = Post::query();
        if(!empty($relations)){
            $query->with($relations);
        }
        if(!empty($filters['title'])){
            $query->where('title', 'LIKE', '%' . $filters['title'] . '%');
        }
        if(!empty($filters['body'])){
            $query->where('body', 'LIKE', '%' . $filters['body'] . '%');
        }
        if(!empty($filters['author_id'])){
            $query->where('author_id', $filters['author_id']);
        }
        return $query->withCount('likes')->latest()->paginate(5);
    }
    public function findById(int $id): ?Post{
        return Post::find($id);
    }
    public function create(array $data): Post{
        return Post::create($data);
    }
    public function update(Post $post, array $data): Post{
        $post->update($data);
        return Post::find($post->id);
    }
    public function delete(Post $post): bool{
       return $post->delete();
    }
}