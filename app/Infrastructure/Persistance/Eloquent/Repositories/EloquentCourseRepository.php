<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Course;

class EloquentCourseRepository implements CourseRepositoryInterface
{
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

    public function findAll(): array
    {
        return Course::all()->all();
    }
}
