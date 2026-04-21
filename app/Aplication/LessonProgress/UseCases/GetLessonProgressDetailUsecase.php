<?php

namespace App\Aplication\LessonProgress\UseCases;

use DomainException;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\Lessons\Exceptions\LessonNotFoundException;
use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Aplication\Enrollment\UseCases\CheckStudentEnrollmentAccessUsecase;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Aplication\LessonProgress\DTOs\GetLessonProgressDetailDto;

class GetLessonProgressDetailUsecase
{
    public function __construct(
        protected LessonRepositoryInterface $lessonRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
        protected CheckStudentEnrollmentAccessUsecase $checkStudentEnrollmentAccessUsecase,
    ) {}

    public function execute(User $student, GetLessonProgressDetailDto $dto): array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        $lesson = $this->lessonRepository->findById($dto->lessonId);

        if (! $lesson) {
            throw new LessonNotFoundException();
        }

        $access = $this->checkStudentEnrollmentAccessUsecase->execute(
            student: $student,
            dto: new CheckStudentEnrollmentAccessDto(
                courseId: $lesson->course_id
            )
        );

        if (! $access['has_access']) {
            throw new DomainException(
                $access['reason'] ?? 'Student tidak memiliki akses ke course ini.'
            );
        }

        $lessonProgress = $this->lessonProgressRepository->findByStudentAndLesson(
            studentId: $student->id,
            lessonId: $dto->lessonId
        );

        return [
            'course_id' => $lesson->course_id,
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type,
                'content' => $lesson->content,
                'video_url' => $lesson->video_url,
                'is_preview' => $lesson->is_preview,
                'position' => $lesson->position,
                'created_at' => $lesson->created_at,
            ],
            'progress' => [
                'status' => $lessonProgress?->status ?? 'not_started',
                'completed_at' => $lessonProgress?->completed_at,
                'last_accessed_at' => $lessonProgress?->last_accessed_at,
            ],
        ];
    }
}
