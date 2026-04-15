<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Exceptions\InvalidCourseStatusException;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Aplication\Courses\DTOs\UpdateCourseStatusDto;
use App\Domain\Courses\Exceptions\CourseNotFoundException;
use App\Domain\Courses\Exceptions\UnauthorizedCourseAccessException;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateCourseStatusUsecase
{
    private const ALLOWED_STATUSES = [
        'draft',
        'published',
        'archived',
    ];


    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function execute(User $user, string $slug, UpdateCourseStatusDto $dto)
    {
        return DB::transaction(function () use ($user, $slug, $dto) {
            if (!$user || $user->role !== 'instructor') {
                throw new UnauthorizedCourseAccessException();
            }

            $course = $this->courseRepository->findBySlug($slug);

            if (!$course) {
                throw new CourseNotFoundException();
            }

            if ((int) $course->instructor_id !== (int) $user->id) {
                throw new UnauthorizedCourseAccessException();
            }

            if (!in_array($dto->status, self::ALLOWED_STATUSES, true)) {
                throw new InvalidCourseStatusException(self::ALLOWED_STATUSES);
            }

            return $this->courseRepository->update($course, [
                'status' => $dto->status,
            ]);
        });
    }
}
