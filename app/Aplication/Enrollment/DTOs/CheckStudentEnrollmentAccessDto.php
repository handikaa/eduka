<?php

namespace App\Aplication\Enrollment\DTOs;

class CheckStudentEnrollmentAccessDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
