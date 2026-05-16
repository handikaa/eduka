<?php

namespace App\Aplication\Courses\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonFileService
{
    public function store(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'file';

        $filename = Str::uuid()->toString() . '.' . $extension;

        $path = 'courses/lessons/files/' . $filename;

        Storage::disk('public')->putFileAs(
            'courses/lessons/files',
            $file,
            $filename
        );

        return asset('storage/' . $path);
    }
}