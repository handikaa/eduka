<?php

namespace App\Aplication\Enrollment\DTOs;

class EnrollStudentDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
