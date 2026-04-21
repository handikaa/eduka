<?php

namespace App\Aplication\Enrollment\DTOs;

class CancelEnrollmentDto
{
    public function __construct(
        public readonly int $courseId,
    ) {}
}
