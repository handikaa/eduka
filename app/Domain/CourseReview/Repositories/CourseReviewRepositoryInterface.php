<?php

namespace App\Domain\CourseReview\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseReviewRepositoryInterface
{
    public function create(array $data): CourseReview;

    public function findById(int $id): ?CourseReview;

    public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?CourseReview;

    public function getActiveByCourseId(int $courseId, int $perPage = 10, int $page = 1): LengthAwarePaginator;

    public function getActiveAverageRatingByCourseId(int $courseId): float;

    public function countActiveByCourseId(int $courseId): int;

    public function update(CourseReview $courseReview, array $data): CourseReview;
    public function findDeletedByStudentAndCourse(int $studentId, int $courseId): ?CourseReview;
}
