<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\ImageRepoInterface;
use App\Models\Image;


class ImageRepo implements ImageRepoInterface{
    public function getAll(array $filters, array $relations){
        $query = Image::query();
        if(!empty($relations)){
            $query->with($relations);
        }
       
        if(!empty($filters['post_id'])){
            $query->where('post_id', $filters['post_id']);
        }
        if(!empty($filters['image_path'])){
            $query->where('image_path', 'LIKE', '%' . $filters['image_path'] . '%');
        }
        return $query->latest()->paginate(5);
    }
    public function findById(string $id): ?Image{
        return Image::find($id);
    }
    public function create(array $data): Image{
        return Image::create($data);
    }
    public function update(Image $image, array $data): Image{
        $image->update($data);
        return Image::find($image->id);
    }
    public function delete(Image $image): bool{
       return $image->delete();
    }
}