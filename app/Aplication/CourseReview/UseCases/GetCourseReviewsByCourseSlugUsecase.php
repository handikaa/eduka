<?php

namespace App\Aplication\CourseReview\UseCases;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Aplication\CourseReview\DTOs\GetCourseReviewsBySlugDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;

class GetCourseReviewsByCourseSlugUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(GetCourseReviewsBySlugDto $dto): LengthAwarePaginator
    {
        $course = $this->courseRepository->findBySlug($dto->courseSlug);

        if (! $course) {
            throw new CourseNotFoundException();
        }

        return $this->courseReviewRepository->getActiveByCourseId(
            courseId: $course->id,
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
