<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;
use Illuminate\Support\Collection;


class EloquentLessonProgressRepository implements LessonProgressRepositoryInterface
{
    public function __construct(
        protected LessonProgress $model
    ) {}


    public function findByStudentAndLesson(int $studentId, int $lessonId): ?LessonProgress
    {
        return $this->model
            ->where('user_id', $studentId)
            ->where('lesson_id', $lessonId)
            ->first();
    }

    public function create(array $data): LessonProgress
    {
        return $this->model->create($data);
    }

    public function update(LessonProgress $lessonProgress, array $data): LessonProgress
    {
        $lessonProgress->update($data);

        return $lessonProgress->refresh();
    }

    public function countCompletedByStudentAndCourse(int $studentId, int $courseId): int
    {
        return $this->model
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->where('lesson_progress.user_id', $studentId)
            ->where('lessons.course_id', $courseId)
            ->where('lesson_progress.status', 'completed')
            ->count();
    }

    public function getByStudentAndLessonIds(int $studentId, array $lessonIds): Collection
    {
        if (empty($lessonIds)) {
            return collect();
        }

        return $this->model
            ->where('user_id', $studentId)
            ->whereIn('lesson_id', $lessonIds)
            ->get();
    }
}
