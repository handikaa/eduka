<?php

namespace App\Aplication\Courses\DTOs;

class CreateCourseDto
{
    public function __construct(
        public int $instructorId,
        public string $title,
        public string $description,
        public string $level,
        public int $price,
        public int $quota,
        public array $categoryIds,
        public ?string $thumbnailUrl = null,
    ) {}
}
