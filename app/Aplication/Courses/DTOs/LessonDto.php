<?php

namespace App\Aplication\Courses\DTOs;

class LessonDTO
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $type,
        public string $content,
        public ?string $video_url,
        public bool $is_preview,
    ) {}
}
