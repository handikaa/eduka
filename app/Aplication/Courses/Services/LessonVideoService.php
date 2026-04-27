<?php

namespace App\Aplication\Courses\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonVideoService
{
    public function store(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'mp4';

        $filename = Str::uuid()->toString() . '.' . $extension;

        $path = 'courses/lessons/videos/' . $filename;

        Storage::disk('public')->putFileAs(
            'courses/lessons/videos',
            $file,
            $filename
        );

        return asset('storage/' . $path);
    }
}