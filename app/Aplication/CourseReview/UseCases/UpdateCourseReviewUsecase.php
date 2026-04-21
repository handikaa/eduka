<?php

namespace App\Aplication\CourseReview\UseCases;

use Illuminate\Support\Facades\DB;
use App\Aplication\CourseReview\DTOs\UpdateCourseReviewDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\CourseReview\Exceptions\CourseReviewNotFoundException;
use App\Domain\CourseReview\Exceptions\InvalidCourseReviewRatingException;
use App\Domain\CourseReview\Exceptions\OnlyStudentCanCreateCourseReviewException;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class UpdateCourseReviewUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $student, UpdateCourseReviewDto $dto): CourseReview
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanCreateCourseReviewException();
            }

            if ($dto->rating < 1 || $dto->rating > 5) {
                throw new InvalidCourseReviewRatingException();
            }

            $course = $this->courseRepository->findById($dto->courseId);

            if (! $course) {
                throw new CourseNotFoundException();
            }

            $review = $this->courseReviewRepository->findActiveByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if (! $review) {
                throw new CourseReviewNotFoundException();
            }

            $updatedReview = $this->courseReviewRepository->update($review, [
                'rating' => $dto->rating,
                'comment' => $dto->comment,
            ]);

            $ratingCount = $this->courseReviewRepository->countActiveByCourseId($dto->courseId);
            $ratingAvg = $this->courseReviewRepository->getActiveAverageRatingByCourseId($dto->courseId);

            $this->courseRepository->update($course, [
                'rating_count' => $ratingCount,
                'rating_avg' => $ratingAvg,
            ]);

            return $updatedReview;
        });
    }
}
