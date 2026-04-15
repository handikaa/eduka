<?php

namespace App\Domain\LessonProgress\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\Lesson;

interface LessonProgressRepositoryInterface
{
    public function countCompletedByStudentAndCourse(int $studentId, int $courseId): int;
}
