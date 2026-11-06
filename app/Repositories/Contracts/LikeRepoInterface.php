<?php
namespace App\Repositories\Contracts;
use App\Models\Like;


interface LikeRepoInterface {
    public function getAll(array $filters, array $relations);
    public function findById(string $id) : ?Like;
    public function create(array $data) : Like;
    public function update(Like $like, array $data) : Like;
    public function delete(Like $like) : bool;
}