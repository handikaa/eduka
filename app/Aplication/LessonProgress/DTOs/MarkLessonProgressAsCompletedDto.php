<?php

namespace App\Aplication\LessonProgress\DTOs;

class MarkLessonProgressAsCompletedDto
{
    public function __construct(
        public readonly int $lessonId,
    ) {}
}
