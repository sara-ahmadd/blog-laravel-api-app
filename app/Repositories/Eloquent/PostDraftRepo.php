<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\PostDraftRepoInterface;
use App\Models\PostDraft;


class PostDraftRepo implements PostDraftRepoInterface{
    public function getAll(array $filters, array $relations){
        $query = PostDraft::query();
        if(!empty($relations)){
            $query->with($relations);
        }
        if(!empty($filters['title'])){
            $query->where('title', 'LIKE', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['tags'])) {
            $tags = $filters['tags'];

            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if(!empty($filters['body'])){
            $query->where('body', 'LIKE', '%' . $filters['body'] . '%');
        }
        if(!empty($filters['description'])){
            $query->where('description', 'LIKE', '%' . $filters['description'] . '%');
        }
        if(!empty($filters['image_alt'])){
            $query->where('image_alt', 'LIKE', '%' . $filters['image_alt'] . '%');
        }
        if(!empty($filters['author_id'])){
            $query->where('author_id', $filters['author_id']);
        }
        return $query->latest()->paginate(5);
    }
    public function findById(int $id): ?PostDraft{
        return PostDraft::find($id);
    }
    public function create(array $data): PostDraft{
        return PostDraft::create($data);
    }
    public function update(PostDraft $postDraft, array $data): PostDraft{
        $postDraft->update($data);
        return PostDraft::find($postDraft->id);
    }
    public function delete(PostDraft $postDraft): bool{
       return $postDraft->delete();
    }
}