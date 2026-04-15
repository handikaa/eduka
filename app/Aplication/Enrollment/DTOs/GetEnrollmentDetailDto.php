<?php

namespace App\Aplication\Enrollment\DTOs;

class GetEnrollmentDetailDto
{
    public function __construct(
        public readonly int $enrollmentId,
    ) {}
}
