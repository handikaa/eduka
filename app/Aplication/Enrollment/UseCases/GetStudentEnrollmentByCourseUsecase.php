<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\GetStudentEnrollmentByCourseDto;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetStudentEnrollmentByCourseUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
    ) {}

    public function execute(User $student, GetStudentEnrollmentByCourseDto $dto): ?Enrollment
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        return $this->enrollmentRepository->findByStudentAndCourse(
            studentId: $student->id,
            courseId: $dto->courseId
        );
    }
}
