<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Aplication\Enrollment\DTOs\GetStudentEnrollmentsDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\User\Exceptions\OnlyStudentCanAccessEnrollmentException;


class GetStudentEnrollmentsUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,

    ) {}

    public function execute(User $student, GetStudentEnrollmentsDto $dto): LengthAwarePaginator
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        return $this->enrollmentRepository->getByStudentId(
            studentId: $student->id,
            perPage: $dto->perPage
        );
    }
}
