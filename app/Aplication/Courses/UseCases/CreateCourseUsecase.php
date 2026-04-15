<?php

namespace App\Aplication\Courses\UseCases;

use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Aplication\Courses\DTOs\CreateCourseDto;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Aplication\Category\Services\SlugService;
use App\Domain\User\Exceptions\OnlyMentorCanCreateCourseException;
use Illuminate\Support\Str;
use App\Domain\Courses\Exceptions\CategoryIsRequiredException;
use App\Domain\User\Repositories\UserRepositoryInterface;


class CreateCourseUsecase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private SlugService $slugService,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(CreateCourseDto $dto)
    {
        // Validasi instructor
        $user = $this->userRepository->findById($dto->instructorId);

        if (!$user || $user->role !== 'instructor') {
            throw new OnlyMentorCanCreateCourseException();
        }

        if (empty($dto->categoryIds)) {
            throw new CategoryIsRequiredException('Category is required.');
        }

        // Generate slug
        $slug = $this->slugService->generate($dto->title);

        // Pastikan slug unik
        $existing = $this->courseRepository->findBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        // Create Course
        return $this->courseRepository->create([
            'instructor_id' => $user->id,
            'title' => $dto->title,
            'slug' => $slug,
            'description' => $dto->description,
            'level' => $dto->level,
            'price' => $dto->price,
            'quota' => $dto->quota,
            'thumbnail_url' => $dto->thumbnailUrl,
            'category_ids' => $dto->categoryIds,
            'status' => 'draft',
            'enrolled_count' => 0,
            'rating_count' => 0,
            'rating_avg' => 0,
        ]);
    }
}
