<?php

namespace App\Services;
use App\Repositories\Contracts\PostRepoInterface;
use App\Models\Post;


class PostService {
    public function __construct(private PostRepoInterface $postRepo){}

    public function exists(array $data) : bool{
        return $this->postRepo->exists($data);
    }
    public function create(array $data) : Post{
        return $this->postRepo->create($data);
    }
    public function findAll(array $filters = [], array $relations=[]) {
        return $this->postRepo->getAll($filters, $relations);
    }
    public function findById(int $id) :?Post{
        return $this->postRepo->findById($id);
    }
    public function update(Post $post, array $data) :Post{
        return $this->postRepo->update($post, $data);
    }
    public function delete(Post $post) :bool{
        return $this->postRepo->delete($post);
    }
}