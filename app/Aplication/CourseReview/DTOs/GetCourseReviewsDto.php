<?php

namespace App\Aplication\CourseReview\DTOs;

class GetCourseReviewsDto
{
    public function __construct(
        public readonly int $courseId,
        public readonly int $perPage = 10,
        public readonly int $page = 1,
    ) {}
}
