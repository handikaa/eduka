<?php

namespace App\Aplication\Enrollment\DTOs;

class GetStudentEnrollmentsDto
{
    public function __construct(
        public readonly int $perPage = 10,
    ) {}
}
