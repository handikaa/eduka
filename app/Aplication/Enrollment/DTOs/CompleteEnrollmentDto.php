<?php

namespace App\Aplication\Enrollment\DTOs;

class CompleteEnrollmentDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
