<?php

namespace App\Services;
use App\Repositories\Contracts\LikeRepoInterface;
use App\Models\Like;


class LikeService {
    public function __construct(private LikeRepoInterface $likeRepo){}

    public function exists(array $data) : bool{
        return $this->likeRepo->exists($data);
    }
    public function create(array $data) : Like{
        return $this->likeRepo->create($data);
    }
    public function findAll(array $filters = [], array $relations=[]) {
        return $this->likeRepo->getAll($filters, $relations);
    }
    public function findById(string $id) :?Like{
        return $this->likeRepo->findById($id);
    }
    public function update(Like $Like, array $data) :Like{
        return $this->likeRepo->update($Like, $data);
    }
    public function delete(Like $Like) :bool{
        return $this->likeRepo->delete($Like);
    }
}