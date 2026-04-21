<?php

namespace App\Domain\Courses\Repositories;


use App\Aplication\Courses\DTOs\GetAllCoursesDto;
use App\Infrastructure\Persistance\Eloquent\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface
{
    public function create(array $data): Course;

    public function findById(int $id): ?Course;

    public function findBySlug(string $slug): ?Course;
    public function delete(int $id): bool;

    public function forceDelete(int $id): bool;

    public function findAll(GetAllCoursesDto $dto): LengthAwarePaginator;


    public function update(Course $course, array $data): Course;

    public function restore(int $id): bool;

    public function incrementEnrolledCount(int $courseId, int $amount = 1): void;

    public function decrementEnrolledCount(int $courseId, int $amount = 1): void;
}
