<?php

namespace App\Aplication\CourseReview\DTOs;

class CreateCourseReviewDto
{
    public function __construct(
        public readonly int $courseId,
        public readonly int $rating,
        public readonly ?string $comment = null,
    ) {}
}
