<?php

namespace App\Aplication\Enrollment\DTOs;

class GetStudentEnrollmentDetailDto
{
    public function __construct(
        public readonly int $enrollmentId,
    ) {}
}
