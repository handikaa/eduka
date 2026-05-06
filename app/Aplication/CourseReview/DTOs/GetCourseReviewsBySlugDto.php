<?php

namespace App\Aplication\CourseReview\DTOs;

class GetCourseReviewsBySlugDto
{
    public function __construct(
        public readonly string $courseSlug,
        public readonly int $perPage = 10,
        public readonly int $page = 1,
    ) {}
}
