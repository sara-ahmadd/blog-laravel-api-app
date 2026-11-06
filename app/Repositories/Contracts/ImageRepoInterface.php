<?php
namespace App\Repositories\Contracts;
use App\Models\Image;


interface ImageRepoInterface {
    public function getAll(array $filters, array $relations);
    public function findById(string $id) : ?Image;
    public function create(array $data) : Image;
    public function update(Image $image, array $data) : Image;
    public function delete(Image $image) : bool;
}