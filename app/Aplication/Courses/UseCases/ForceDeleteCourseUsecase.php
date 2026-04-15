<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Exceptions\CourseNotFoundException;

class ForceDeleteCourseUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(int $id)
    {
        $deleted = $this->courseRepository->forceDelete($id);

        if (!$deleted) {
            throw new CourseNotFoundException('Course not found');
        }

        return true;
    }
}
