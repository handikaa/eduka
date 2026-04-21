<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\GetEnrollmentDetailDto;
use App\Domain\Enrollment\Exceptions\EnrollmentNotFoundException;
use App\Domain\Enrollment\Exceptions\UnauthorizedToViewEnrollmentDetailException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetEnrollmentDetailUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
    ) {}

    public function execute(User $authUser, GetEnrollmentDetailDto $dto): Enrollment
    {
        $enrollment = $this->enrollmentRepository->findById($dto->enrollmentId);

        if (! $enrollment) {
            throw new EnrollmentNotFoundException();
        }

        $isStudentOwner = (
            $authUser->role === 'student' &&
            (int) $enrollment->user_id === (int) $authUser->id
        );

        $isInstructorOwner = (
            $authUser->role === 'instructor' &&
            $enrollment->course &&
            (int) $enrollment->course->instructor_id === (int) $authUser->id
        );

        if (! $isStudentOwner && ! $isInstructorOwner) {
            throw new UnauthorizedToViewEnrollmentDetailException();
        }

        return $enrollment;
    }
}
