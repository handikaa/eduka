<?php

namespace App\Aplication\CourseReview\UseCases;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Aplication\CourseReview\DTOs\GetCourseReviewsDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;

class GetCourseReviewsUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(GetCourseReviewsDto $dto): LengthAwarePaginator
    {
        $course = $this->courseRepository->findById($dto->courseId);

        if (! $course) {
            throw new CourseNotFoundException();
        }

        return $this->courseReviewRepository->getActiveByCourseId(
            courseId: $dto->courseId,
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
