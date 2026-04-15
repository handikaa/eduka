<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\EnrollStudentDto;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Domain\User\Exceptions\OnlyStudentCanEnrollCourseException;
use App\Domain\User\Exceptions\StudentAlreadyEnrollException;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Enrollment\Exceptions\CourseQuotaIsFullException;
use DomainException;

class EnrollStudentUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected UserRepositoryInterface $userRepository,
        protected CourseRepositoryInterface $courseRepository,
    ) {}

    public function execute(User $student, EnrollStudentDto $dto): Enrollment
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanEnrollCourseException();
            }

            $course = $this->courseRepository->findById($dto->courseId);

            if (! $course) {
                throw new CourseNotFoundException();
            }

            $alreadyEnrolled = $this->enrollmentRepository->existsByStudentAndCourse(
                $student->id,
                $dto->courseId
            );

            if ($alreadyEnrolled) {
                throw new StudentAlreadyEnrollException();
            }

            if ($course->enrolled_count >= $course->quota) {
                throw new CourseQuotaIsFullException();
            }

            $enrollment = $this->enrollmentRepository->create([
                'user_id'      => $student->id,
                'course_id'    => $dto->courseId,
                'status'       => 'active',
                'enrolled_at'  => now(),
                'completed_at' => null,

            ]);

            $this->courseRepository->incrementEnrolledCount($course->id);

            return $enrollment;
        });
    }
}
