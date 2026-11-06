<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\CommentRepoInterface;
use App\Models\Comment;


class CommentRepo implements CommentRepoInterface{
    public function getAll(array $filters, array $relations){
        $query = Comment::query();
        if(!empty($relations)){
            $query->with($relations);
        }
        if(!empty($filters['content'])){
            $query->where('content', 'LIKE', '%' . $filters['content'] . '%');
        }
        if(!empty($filters['post_id'])){
            $query->where('post_id',$filters['post_id']);
        }
        if(!empty($filters['user_id'])){
            $query->where('user_id',$filters['user_id']);
        }
        
        return $query->whereNull('parent_id')->paginate();
    }
    public function findById(string $id): ?Comment{
        return Comment::find($id);
    }
    public function create(array $data): Comment{
        return Comment::create($data);
    }
    public function update(Comment $comment, array $data): Comment{
        $comment->update($data);
        return Comment::find($comment->id);
    }
    public function delete(Comment $comment): bool{
       return $comment->delete();
    }
}