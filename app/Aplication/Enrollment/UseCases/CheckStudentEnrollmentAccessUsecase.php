<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class CheckStudentEnrollmentAccessUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
    ) {}

    public function execute(User $student, CheckStudentEnrollmentAccessDto $dto): array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        $enrollment = $this->enrollmentRepository->findByStudentAndCourse(
            studentId: $student->id,
            courseId: $dto->courseId
        );

        if (! $enrollment) {
            return [
                'has_access' => false,
                'status' => null,
                'enrollment' => null,
                'reason' => 'Enrollment not found.',
            ];
        }

        if ($enrollment->isCancelled()) {
            return [
                'has_access' => false,
                'status' => $enrollment->status,
                'enrollment' => $enrollment,
                'reason' => 'Enrollment is cancelled.',
            ];
        }

        return [
            'has_access' => true,
            'status' => $enrollment->status,
            'enrollment' => $enrollment,
            'reason' => null,
        ];
    }
}
