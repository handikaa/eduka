<?php


namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\GetCourseEnrollmentsDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Enrollment\Exceptions\InstructorCannotAccessOtherCourseEnrollmentsException;
use App\Domain\Enrollment\Exceptions\OnlyInstructorCanAccessCourseEnrollmentsException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCourseEnrollmentsUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $instructor, GetCourseEnrollmentsDto $dto): LengthAwarePaginator
    {
        if ($instructor->role !== 'instructor') {
            throw new OnlyInstructorCanAccessCourseEnrollmentsException();
        }

        $course = $this->courseRepository->findById($dto->courseId);

        if (! $course) {
            throw new CourseNotFoundException();
        }

        if ((int) $course->instructor_id !== (int) $instructor->id) {
            throw new InstructorCannotAccessOtherCourseEnrollmentsException();
        }

        return $this->enrollmentRepository->getByCourseId(
            courseId: $dto->courseId,
            perPage: $dto->perPage
        );
    }
}
