<?php

namespace App\Aplication\Dashboard\UseCases;

use App\Aplication\Dashboard\DTOs\GetStudentContinueLearningDto;
use App\Domain\Dashboard\Exceptions\OnlyStudentCanAccessDashboardException;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetStudentContinueLearningUsecase
{
    public function __construct(
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
    ) {}

    public function execute(User $student, GetStudentContinueLearningDto $dto): ?array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessDashboardException();
        }

        $latestAccessedProgress = $this->lessonProgressRepository->findLatestAccessedByStudentId($student->id);

        if (! $latestAccessedProgress || ! $latestAccessedProgress->lesson || ! $latestAccessedProgress->lesson->course) {
            return null;
        }

        $lesson = $latestAccessedProgress->lesson;
        $course = $lesson->course;

        return [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'thumbnail_url' => $course->thumbnail_url,
                'status' => $course->status,
            ],
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type,
                'position' => $lesson->position,
            ],
            'progress' => [
                'status' => $latestAccessedProgress->status,
                'completed_at' => $latestAccessedProgress->completed_at,
                'last_accessed_at' => $latestAccessedProgress->last_accessed_at,
            ],
        ];
    }
}
