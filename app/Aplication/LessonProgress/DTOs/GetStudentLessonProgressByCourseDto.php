<?php

namespace App\Aplication\LessonProgress\DTOs;

class GetStudentLessonProgressByCourseDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
