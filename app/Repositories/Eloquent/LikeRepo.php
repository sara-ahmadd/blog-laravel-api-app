<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\LikeRepoInterface;
use App\Models\Like;


class LikeRepo implements LikeRepoInterface{
    public function exists(array $conditions):bool{
        return Like::where($conditions)->exists();
    }
    public function getAll(array $filters, array $relations){
        $query = Like::query();
        if(!empty($relations)){
            $query->with($relations);
        }
        
        if(!empty($filters['user_id'])){
            $query->where('user_id', $filters['user_id']);
        }
        if(!empty($filters['post_id'])){
            $query->where('post_id', $filters['post_id']);
        }
        return $query->paginate(5);
    }
    public function findById(string $id): ?Like{
        return Like::find($id);
    }
    public function create(array $data): Like{
        return Like::create($data);
    }
    public function update(Like $like, array $data): Like{
        $like->update($data);
        return Like::find($like->id);
    }
    public function delete(Like $like): bool{
       return $like->delete();
    }
}