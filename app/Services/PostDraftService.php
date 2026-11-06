<?php

namespace App\Services;
use App\Repositories\Contracts\PostDraftRepoInterface;
use App\Models\PostDraft;


class PostDraftService {
    public function __construct(private PostDraftRepoInterface $postDraftRepo){}

    public function create(array $data) : PostDraft{
        return $this->postDraftRepo->create($data);
    }
    public function findAll(array $filters = [], array $relations=[]) {
        return $this->postDraftRepo->getAll($filters, $relations);
    }
    public function findById(int $id) :?PostDraft{
        return $this->postDraftRepo->findById($id);
    }
    public function update(PostDraft $postDraft, array $data) :PostDraft{
        return $this->postDraftRepo->update($postDraft, $data);
    }
    public function delete(PostDraft $postDraft) :bool{
        return $this->postDraftRepo->delete($postDraft);
    }
}