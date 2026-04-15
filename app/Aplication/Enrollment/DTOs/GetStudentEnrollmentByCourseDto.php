<?php

namespace App\Aplication\Enrollment\DTOs;

class GetStudentEnrollmentByCourseDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
