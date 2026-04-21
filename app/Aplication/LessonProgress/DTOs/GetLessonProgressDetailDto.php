<?php

namespace App\Aplication\LessonProgress\DTOs;

class GetLessonProgressDetailDto
{
    public function __construct(
        public readonly int $lessonId,
    ) {}
}
