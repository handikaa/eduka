<?php

namespace App\Domain\Courses\Repositories;

use Illuminate\Support\Collection;

use App\Infrastructure\Persistance\Eloquent\Models\Lesson;

interface LessonRepositoryInterface
{
    public function create(array $data): Lesson;

    public function countByCourseId(int $courseId): int;
    public function findById(int $lessonsId): ?Lesson;
    public function getByCourseId(int $courseId): Collection;
}
