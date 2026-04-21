<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentEnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function __construct(
        protected Enrollment $model
    ) {}

    /**
     * Check whether student already enrolled in a course.
     */
    public function existsByStudentAndCourse(int $studentId, int $courseId): bool
    {
        return $this->model
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Create new enrollment.
     */
    public function create(array $data): Enrollment
    {
        return $this->model->create($data);
    }

    /**
     * Find enrollment by student and course.
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment
    {
        return $this->model
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * Get enrollments owned by a student.
     */
    public function getByStudentId(int $studentId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['course'])
            ->where('user_id', $studentId)
            ->latest('enrolled_at')
            ->paginate($perPage);
    }

    /**
     * Get students who bought/enrolled in a course.
     */
    public function getByCourseId(int $courseId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['user'])
            ->where('course_id', $courseId)
            ->latest('enrolled_at')
            ->paginate($perPage);
    }

    /**
     * Count active enrollments in a course.
     */
    public function countActiveByCourseId(int $courseId): int
    {
        return $this->model
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->count();
    }

    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        $enrollment->update($data);

        return $enrollment->refresh();
    }

    public function findById(int $id): ?Enrollment
    {
        return $this->model
            ->with(['user', 'course'])
            ->find($id);
    }
}
