<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\CancelEnrollmentDto;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Enrollment\Exceptions\CompletedEnrollmentCannotBeCancelledException;
use App\Domain\Enrollment\Exceptions\EnrollmentAlreadyCancelledException;
use App\Domain\Enrollment\Exceptions\EnrollmentNotFoundException;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanCancelEnrollmentException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Support\Facades\DB;

class CancelEnrollmentUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $student, CancelEnrollmentDto $dto): Enrollment
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanCancelEnrollmentException();
            }

            $enrollment = $this->enrollmentRepository->findByStudentAndCourse(
                studentId: $student->id,
                courseId: $dto->courseId
            );

            if (! $enrollment) {
                throw new EnrollmentNotFoundException();
            }

            if ($enrollment->isCancelled()) {
                throw new EnrollmentAlreadyCancelledException();
            }

            if ($enrollment->isCompleted()) {
                throw new CompletedEnrollmentCannotBeCancelledException();
            }

            $updatedEnrollment = $this->enrollmentRepository->update($enrollment, [
                'status' => 'cancelled',
            ]);

            $this->courseRepository->decrementEnrolledCount($dto->courseId);

            return $updatedEnrollment;
        });
    }
}
