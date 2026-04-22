<?php

namespace App\Domain\LessonProgress\Repositories;

use Illuminate\Support\Collection;

use App\Infrastructure\Persistance\Eloquent\Models\Lesson;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;

interface LessonProgressRepositoryInterface
{
    public function findByStudentAndLesson(int $studentId, int $lessonId): ?LessonProgress;

    public function create(array $data): LessonProgress;

    public function update(LessonProgress $lessonProgress, array $data): LessonProgress;

    public function countCompletedByStudentAndCourse(int $studentId, int $courseId): int;

    public function getByStudentAndLessonIds(int $studentId, array $lessonIds): Collection;
    public function findLatestAccessedByStudentId(int $studentId): ?LessonProgress;
}
