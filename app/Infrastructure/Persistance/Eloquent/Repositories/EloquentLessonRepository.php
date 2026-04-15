<?php

namespace App\Infrastructure\Persistance\Eloquent\Repositories;

use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\Lesson;

class EloquentLessonRepository implements LessonRepositoryInterface
{
    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }
}
