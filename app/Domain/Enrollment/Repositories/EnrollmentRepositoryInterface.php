<?php

namespace App\Domain\Enrollment\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EnrollmentRepositoryInterface
{
    /**
     * Check whether student already enrolled in a course.
     */
    public function existsByStudentAndCourse(int $studentId, int $courseId): bool;

    /**
     * Create new enrollment.
     */
    public function create(array $data): Enrollment;

    /**
     * Find enrollment by student and course.
     */
    public function findByStudentAndCourse(int $studentId, int $courseId): ?Enrollment;

    /**
     * Get enrollments owned by a student.
     */
    public function getByStudentId(int $studentId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get students who bought/enrolled in a course.
     */
    public function getByCourseId(int $courseId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Count active enrollments in a course.
     */
    public function countActiveByCourseId(int $courseId): int;

    public function update(Enrollment $enrollment, array $data): Enrollment;

    // public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?Enrollment;

    public function findById(int $id): ?Enrollment;
}
