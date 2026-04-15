<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;

class GetCourseBySlugUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(string $slug)
    {
        $course = $this->courseRepository->findBySlug($slug);

        if (!$course) {
            throw new CourseNotFoundException('Course not found');
        }

        return $course;
    }
}
