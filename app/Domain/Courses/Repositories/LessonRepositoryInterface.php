<?php

namespace App\Domain\Courses\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\Lesson;

interface LessonRepositoryInterface
{
    public function create(array $data): Lesson;

    public function countByCourseId(int $courseId): int;
}
