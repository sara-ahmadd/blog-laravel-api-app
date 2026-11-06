<?php

namespace App\Services;
use App\Repositories\Contracts\CommentRepoInterface;
use App\Models\Comment;


class CommentService {
    public function __construct(private CommentRepoInterface $commentRepo){}

    public function create(array $data) : Comment{
        return $this->commentRepo->create($data);
    }
    public function findAll(array $filters = [], array $relations=[]) {
        return $this->commentRepo->getAll($filters, $relations);
    }
    public function findById(string $id) :?Comment{
        return $this->commentRepo->findById($id);
    }
    public function update(Comment $Comment, array $data) :Comment{
        return $this->commentRepo->update($Comment, $data);
    }
    public function delete(Comment $Comment) :bool{
        return $this->commentRepo->delete($Comment);
    }
}