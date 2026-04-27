<?php

namespace App\Aplication\Courses\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CourseThumbnailService
{
    public function store(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        $filename = Str::uuid()->toString() . '.webp';
        $path = 'courses/thumbnails/' . $filename;

        $image = Image::decode($file)
            ->scaleDown(width: 1280);

        $encodedImage = $image->encodeUsingFileExtension('webp', quality: 75);

        Storage::disk('public')->put($path, (string) $encodedImage);

        return asset('storage/' . $path);
    }
}