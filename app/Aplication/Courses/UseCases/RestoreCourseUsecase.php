<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Exceptions\CourseNotFoundException;

class RestoreCourseUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(int $id)
    {
        $restored = $this->courseRepository->restore($id);

        if (!$restored) {
            throw new CourseNotFoundException('Course not found');
        }

        return true;
    }
}
