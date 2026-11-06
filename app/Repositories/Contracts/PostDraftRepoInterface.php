<?php
namespace App\Repositories\Contracts;
use App\Models\PostDraft;


interface PostDraftRepoInterface {
    public function getAll(array $filters, array $relations);
    public function findById(int $id) : ?PostDraft;
    public function create(array $data) : PostDraft;
    public function update(PostDraft $postDraft, array $data) : PostDraft;
    public function delete(PostDraft $postDraft) : bool;
}