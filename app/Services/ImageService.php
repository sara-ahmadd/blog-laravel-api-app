<?php

namespace App\Services;
use App\Repositories\Contracts\ImageRepoInterface;
use App\Models\Image;


class ImageService {
    public function __construct(private ImageRepoInterface $imageRepo){}

    public function create(array $data) : Image{
        return $this->imageRepo->create($data);
    }
    public function findAll(array $filters = [], array $relations=[]) {
        return $this->imageRepo->getAll($filters, $relations);
    }
    public function findById(string $id) :?Image{
        return $this->imageRepo->findById($id);
    }
    public function update(Image $Image, array $data) :Image{
        return $this->imageRepo->update($Image, $data);
    }
    public function delete(Image $Image) :bool{
        return $this->imageRepo->delete($Image);
    }
}