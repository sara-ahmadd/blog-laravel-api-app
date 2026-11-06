<?php
namespace App\Repositories\Contracts;
use App\Models\Post;


interface PostRepoInterface {
    public function getAll(array $filters, array $relations);
    public function findById(int $id) : ?Post;
    public function create(array $data) : Post;
    public function update(Post $post, array $data) : Post;
    public function delete(Post $post) : bool;
}