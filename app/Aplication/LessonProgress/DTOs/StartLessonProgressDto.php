<?php

namespace App\Aplication\LessonProgress\DTOs;

class StartLessonProgressDto
{
    public function __construct(
        public readonly int $lessonId,
    ) {}
}
