<?php

namespace App\Aplication\CourseReview\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\CourseReview\Exceptions\DeletedCourseReviewNotFoundException;
use App\Domain\CourseReview\Exceptions\OnlyStudentCanCreateCourseReviewException;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class RestoreCourseReviewUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $student, int $courseId): CourseReview
    {
        return DB::transaction(function () use ($student, $courseId) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanCreateCourseReviewException();
            }

            $course = $this->courseRepository->findById($courseId);

            if (! $course) {
                throw new CourseNotFoundException();
            }

            $review = $this->courseReviewRepository->findDeletedByStudentAndCourse(
                studentId: $student->id,
                courseId: $courseId
            );

            if (! $review) {
                throw new DeletedCourseReviewNotFoundException();
            }

            $restoredReview = $this->courseReviewRepository->update($review, [
                'is_delete' => false,
            ]);

            $ratingCount = $this->courseReviewRepository->countActiveByCourseId($courseId);
            $ratingAvg = $this->courseReviewRepository->getActiveAverageRatingByCourseId($courseId);

            $this->courseRepository->update($course, [
                'rating_count' => $ratingCount,
                'rating_avg' => $ratingAvg,
            ]);

            return $restoredReview;
        });
    }
}
