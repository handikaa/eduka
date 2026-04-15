<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;

class GetCourseByIdUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(int $id)
    {
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            throw new CourseNotFoundException('Course not found');
        }

        return $course;
    }
}
