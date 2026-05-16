<?php

namespace App\Aplication\Courses\DTOs;

class CreateLessonDto
{
    public function __construct(
        public int $courseId,
        public string $title,
        public ?string $content,
        public string $type,
        public ?string $videoUrl,
        public ?string $fileUrl,
        public bool $isPreview,
        public int $position,
    ) {}
}