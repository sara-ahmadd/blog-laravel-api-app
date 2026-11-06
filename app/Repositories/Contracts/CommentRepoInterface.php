<?php
namespace App\Repositories\Contracts;
use App\Models\Comment;


interface CommentRepoInterface {
    public function getAll(array $filters, array $relations);
    public function findById(string $id) : ?Comment;
    public function create(array $data) : Comment;
    public function update(Comment $comment, array $data) : Comment;
    public function delete(Comment $comment) : bool;
}