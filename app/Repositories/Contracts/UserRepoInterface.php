<?php
namespace App\Repositories\Contracts;
use App\Models\User;


interface UserRepoInterface {
    public function getAll(array $filters);
    public function findById(int $id, array $relations) : ?User;
    public function create(array $data) : User;
    public function update(User $user, array $data) : User;
    public function delete(User $user) : bool;
}