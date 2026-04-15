<?php

namespace App\Aplication\Courses\DTOs;

class UpdateCourseStatusDto
{
    public function __construct(
        public string $status
    ) {}
}
