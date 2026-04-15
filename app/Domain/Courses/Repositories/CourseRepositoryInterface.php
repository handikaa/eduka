<?php

namespace App\Domain\Courses\Repositories;

use App\Infrastructure\Persistance\Eloquent\Models\Course;

interface CourseRepositoryInterface
{
    public function create(array $data): Course;

    public function findById(int $id): ?Course;

    public function findBySlug(string $slug): ?Course;
    public function delete(int $id): bool;

    public function forceDelete(int $id): bool;


    public function findAll(): array;

    public function update(Course $course, array $data): Course;

    public function restore(int $id): bool;
}
