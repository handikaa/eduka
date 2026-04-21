<?php

namespace App\Aplication\LessonProgress\DTOs;

class UpdateLastAccessedLessonProgressDto
{
    public function __construct(
        public readonly int $lessonId,
    ) {}
}
