<?php

namespace App\Aplication\CourseReview\UseCases;

use Illuminate\Support\Facades\DB;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Aplication\CourseReview\DTOs\CreateCourseReviewDto;
use App\Domain\CourseReview\Exceptions\InvalidCourseReviewRatingException;
use App\Domain\CourseReview\Exceptions\OnlyStudentCanCreateCourseReviewException;
use App\Domain\CourseReview\Exceptions\StudentAlreadyReviewedCourseException;
use App\Domain\CourseReview\Exceptions\StudentMustCompleteCourseBeforeReviewException;

class CreateCourseReviewUsecase
{
    public function __construct(
        protected CourseReviewRepositoryInterface $courseReviewRepository,
        protected CourseRepositoryInterface $courseRepository,
        protected EnrollmentRepositoryInterface $enrollmentRepository,
    ) {}

    public function execute(User $student, CreateCourseReviewDto $dto): CourseReview
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

            $enrollment = $this->enrollmentRepository->findByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if (! $enrollment || $enrollment->status !== 'completed') {
                throw new StudentMustCompleteCourseBeforeReviewException();
            }

            $existingReview = $this->courseReviewRepository->findActiveByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if ($existingReview) {
                throw new StudentAlreadyReviewedCourseException();
            }

            $review = $this->courseReviewRepository->create([
                'course_id' => $dto->courseId,
                'user_id' => $student->id,
                'rating' => $dto->rating,
                'comment' => $dto->comment,
                'is_delete' => false,
            ]);

            $ratingCount = $this->courseReviewRepository->countActiveByCourseId($dto->courseId);
            $ratingAvg = $this->courseReviewRepository->getActiveAverageRatingByCourseId($dto->courseId);

            $this->courseRepository->update($course, [
                'rating_count' => $ratingCount,
                'rating_avg' => $ratingAvg,
            ]);

            return $review;
        });
    }
}
