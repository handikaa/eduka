<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;

class EloquentLessonProgressRepository implements LessonProgressRepositoryInterface
{
    public function __construct(
        protected LessonProgress $model
    ) {}

    public function countCompletedByStudentAndCourse(int $studentId, int $courseId): int
    {
        return $this->model
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->where('lesson_progress.user_id', $studentId)
            ->where('lessons.course_id', $courseId)
            ->where('lesson_progress.status', 'completed')
            ->count();
    }
}
