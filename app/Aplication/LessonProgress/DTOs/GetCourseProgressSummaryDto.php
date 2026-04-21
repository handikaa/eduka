<?php

namespace App\Aplication\LessonProgress\DTOs;

class GetCourseProgressSummaryDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
