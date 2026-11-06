<?php

namespace App\Traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasImage
{
    public function getStoredImagePath(UploadedFile $image, string $directory){
        $imgOriginalName = $image->getClientOriginalName();
    
        $safeName = preg_replace('/[^0-9a-zA-Z.-_]/', '_', pathinfo($imgOriginalName, PATHINFO_FILENAME));
        $extension = pathinfo($imgOriginalName, PATHINFO_EXTENSION);

        $imgName = $safeName . '_' . now()->format('Y.m.d-H:i')."_".Str::random(16) ."." . $extension;
        $imagePath = $image->storeAs( $directory,$imgName, 'public');
        return $imagePath;
    }
}
