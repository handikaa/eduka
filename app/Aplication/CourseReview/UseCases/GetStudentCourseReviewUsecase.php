<?php

namespace App\Aplication\CourseReview\UseCases;

use App\Aplication\CourseReview\DTOs\GetStudentCourseReviewDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Domain\CourseReview\Exceptions\OnlyStudentCanCreateCourseReviewException;
use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetStudentCourseReviewUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $student, GetStudentCourseReviewDto $dto): ?CourseReview
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanCreateCourseReviewException();
        }

        $course = $this->courseRepository->findById($dto->courseId);

        if (! $course) {
            throw new CourseNotFoundException();
        }

        return $this->courseReviewRepository->findActiveByStudentAndCourse(
            studentId: $student->id,
            courseId: $dto->courseId
        );
    }
}
