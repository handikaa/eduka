<?php

namespace App\Aplication\CourseReview\DTOs;

class GetStudentCourseReviewDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
