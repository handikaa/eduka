<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\CourseReview;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCourseReviewRepository implements CourseReviewRepositoryInterface
{
    public function __construct(
        protected CourseReview $model
    ) {}

    public function create(array $data): CourseReview
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?CourseReview
    {
        return $this->model->with(['user', 'course'])->find($id);
    }

    public function findActiveByStudentAndCourse(int $studentId, int $courseId): ?CourseReview
    {
        return $this->model
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->where('is_delete', false)
            ->first();
    }

    public function getActiveByCourseId(int $courseId, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->model
            ->with(['user'])
            ->where('course_id', $courseId)
            ->where('is_delete', false)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getActiveAverageRatingByCourseId(int $courseId): float
    {
        return (float) $this->model
            ->where('course_id', $courseId)
            ->where('is_delete', false)
            ->avg('rating');
    }

    public function countActiveByCourseId(int $courseId): int
    {
        return $this->model
            ->where('course_id', $courseId)
            ->where('is_delete', false)
            ->count();
    }

    public function update(CourseReview $courseReview, array $data): CourseReview
    {
        $courseReview->update($data);

        return $courseReview->refresh();
    }

    public function findDeletedByStudentAndCourse(int $studentId, int $courseId): ?CourseReview
    {
        return $this->model
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->where('is_delete', true)
            ->first();
    }

    public function countActiveByCourseIds(array $courseIds): int
    {
        if (empty($courseIds)) {
            return 0;
        }

        return $this->model
            ->whereIn('course_id', $courseIds)
            ->where('is_delete', false)
            ->count();
    }

    public function getActiveAverageRatingByCourseIds(array $courseIds): float
    {
        if (empty($courseIds)) {
            return 0;
        }

        return (float) $this->model
            ->whereIn('course_id', $courseIds)
            ->where('is_delete', false)
            ->avg('rating');
    }
}
