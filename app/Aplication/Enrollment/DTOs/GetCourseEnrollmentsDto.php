<?php

namespace App\Aplication\Enrollment\DTOs;

class GetCourseEnrollmentsDto
{
    public function __construct(
        public readonly int $courseId,
        public readonly int $perPage = 10,
    ) {}
}
