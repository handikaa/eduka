<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use Illuminate\Support\Collection;

use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Lesson;

class EloquentLessonRepository implements LessonRepositoryInterface
{
    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }
    public function countByCourseId(int $courseId): int
    {
        return Lesson::where('course_id', $courseId)->count();
    }

    public function findById(int $lessonsId): ?Lesson
    {
        return Lesson::find($lessonsId);
    }
    public function getByCourseId(int $courseId): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->orderByRaw('CASE WHEN position IS NULL THEN 1 ELSE 0 END')
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }
}
