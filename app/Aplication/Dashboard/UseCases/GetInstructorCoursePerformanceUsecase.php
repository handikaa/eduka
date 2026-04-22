<?php

namespace App\Aplication\Dashboard\UseCases;

use App\Aplication\Dashboard\DTOs\GetInstructorCoursePerformanceDto;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Dashboard\Exceptions\OnlyInstructorCanAccessDashboardException;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetInstructorCoursePerformanceUsecase
{
    public function __construct(
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $instructor, GetInstructorCoursePerformanceDto $dto): array
    {
        if ($instructor->role !== 'instructor') {
            throw new OnlyInstructorCanAccessDashboardException();
        }

        return $this->courseRepository->getPerformanceByInstructorId($instructor->id);
    }
}
