<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Aplication\Courses\DTOs\GetAllCoursesDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentCourseRepository implements CourseRepositoryInterface
{
    public function __construct(
        protected Course $model
    ) {}

    public function create(array $data): Course
    {

        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $course = Course::create($data);

        if (!empty($categoryIds)) {
            $course->categories()->sync($categoryIds);
        }

        return $course->load(['categories', 'lessons']);
    }

    public function findById(int $id): ?Course
    {
        return Course::with(['lessons', 'categories'])->find($id);
    }


    public function findBySlug(string $slug): ?Course
    {
        return Course::with(['lessons', 'categories'])->where('slug', $slug)->first();
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function delete(int $id): bool
    {
        $course = Course::find($id);

        if (!$course) {
            return false;
        }

        return $course->delete();
    }

    public function forceDelete(int $id): bool
    {
        $course = Course::withTrashed()->find($id);

        if (!$course) {
            return false;
        }

        return $course->forceDelete();
    }

    public function restore(int $id): bool
    {
        $course = Course::onlyTrashed()->find($id);

        if (!$course) {
            return false;
        }

        return $course->restore();
    }


    public function incrementEnrolledCount(int $courseId, int $amount = 1): void
    {
        $this->model
            ->where('id', $courseId)
            ->increment('enrolled_count', $amount);
    }

    public function decrementEnrolledCount(int $courseId, int $amount = 1): void
    {
        $this->model
            ->where('id', $courseId)
            ->where('enrolled_count', '>', 0)
            ->decrement('enrolled_count', $amount);
    }



    public function findAll(GetAllCoursesDto $dto): LengthAwarePaginator
    {
        $allowedSortBy = [
            'created_at',
            'title',
            'price',
            'rating_avg',
            'enrolled_count',
        ];

        $sortBy = in_array($dto->sortBy, $allowedSortBy, true)
            ? $dto->sortBy
            : 'created_at';

        $sortDirection = $dto->sortDirection === 'asc' ? 'asc' : 'desc';

        $query = $this->model->newQuery()
            ->with(['categories', 'lessons', 'instructor']);

        if (!is_null($dto->userId)) {
            $query->where('instructor_id', $dto->userId);
        }

        if (!is_null($dto->status)) {
            $query->where('status', $dto->status);
        }

        if (!is_null($dto->level)) {
            $query->where('level', $dto->level);
        }

        if (!is_null($dto->search)) {
            $query->where(function ($q) use ($dto) {
                $q->where('title', 'like', "%{$dto->search}%")
                    ->orWhere('description', 'like', "%{$dto->search}%");
            });
        }

        if (!is_null($dto->categoryId)) {
            $query->whereHas('categories', function ($q) use ($dto) {
                $q->where('categories.id', $dto->categoryId);
            });
        }

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }
    public function countByInstructorId(int $instructorId): int
    {
        return $this->model
            ->where('instructor_id', $instructorId)
            ->count();
    }

    public function countByInstructorIdAndStatus(int $instructorId, string $status): int
    {
        return $this->model
            ->where('instructor_id', $instructorId)
            ->where('status', $status)
            ->count();
    }

    public function getIdsByInstructorId(int $instructorId): array
    {
        return $this->model
            ->where('instructor_id', $instructorId)
            ->pluck('id')
            ->all();
    }
    public function getPerformanceByInstructorId(int $instructorId): array
    {
        $courses = $this->model
            ->withCount('lessons')
            ->where('instructor_id', $instructorId)
            ->orderByDesc('created_at')
            ->get();

        return $courses->map(function ($course) {
            return [
                'course_id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'status' => $course->status,
                'price' => $course->price,
                'enrolled_count' => $course->enrolled_count,
                'rating_count' => $course->rating_count,
                'rating_avg' => $course->rating_avg,
                'total_lessons' => $course->lessons_count,
                'created_at' => $course->created_at,
            ];
        })->toArray();
    }
}
