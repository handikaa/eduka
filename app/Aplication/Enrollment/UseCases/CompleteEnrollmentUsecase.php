<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\CompleteEnrollmentDto;
use App\Domain\Enrollment\Exceptions\CancelledEnrollmentCannotBeCompletedException;
use App\Domain\Enrollment\Exceptions\EnrollmentAlreadyCompletedException;
use App\Domain\Enrollment\Exceptions\EnrollmentLessonsNotCompletedException;
use App\Domain\Enrollment\Exceptions\EnrollmentNotFoundException;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanCompleteEnrollmentException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Support\Facades\DB;

class CompleteEnrollmentUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected LessonRepositoryInterface $lessonRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
    ) {}

    public function execute(User $student, CompleteEnrollmentDto $dto): Enrollment
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanCompleteEnrollmentException();
            }

            $enrollment = $this->enrollmentRepository->findByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if (! $enrollment) {
                throw new EnrollmentNotFoundException();
            }

            if ($enrollment->isCompleted()) {
                throw new EnrollmentAlreadyCompletedException();
            }

            if ($enrollment->isCancelled()) {
                throw new CancelledEnrollmentCannotBeCompletedException();
            }

            $totalLessons = $this->lessonRepository->countByCourseId($dto->courseId);
            $completedLessons = $this->lessonProgressRepository->countCompletedByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if ($totalLessons === 0 || $completedLessons < $totalLessons) {
                throw new EnrollmentLessonsNotCompletedException();
            }

            return $this->enrollmentRepository->update($enrollment, [
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });
    }
}
