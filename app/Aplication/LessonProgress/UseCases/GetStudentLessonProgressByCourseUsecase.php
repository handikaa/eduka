<?php

namespace App\Aplication\LessonProgress\UseCases;

use DomainException;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Aplication\Enrollment\UseCases\CheckStudentEnrollmentAccessUsecase;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Aplication\LessonProgress\DTOs\GetStudentLessonProgressByCourseDto;

class GetStudentLessonProgressByCourseUsecase
{
    public function __construct(
        protected LessonRepositoryInterface $lessonRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
        protected CheckStudentEnrollmentAccessUsecase $checkStudentEnrollmentAccessUsecase,
    ) {}

    public function execute(User $student, GetStudentLessonProgressByCourseDto $dto): array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        $access = $this->checkStudentEnrollmentAccessUsecase->execute(
            student: $student,
            dto: new CheckStudentEnrollmentAccessDto(
                courseId: $dto->courseId
            )
        );

        if (! $access['has_access']) {
            throw new DomainException(
                $access['reason'] ?? 'Student tidak memiliki akses ke course ini.'
            );
        }

        $lessons = $this->lessonRepository->getByCourseId($dto->courseId);
        $lessonIds = $lessons->pluck('id')->all();

        $lessonProgressCollection = $this->lessonProgressRepository->getByStudentAndLessonIds(
            studentId: $student->id,
            lessonIds: $lessonIds
        );

        $lessonProgressMap = $lessonProgressCollection->keyBy('lesson_id');

        $lessonItems = $lessons->map(function ($lesson) use ($lessonProgressMap) {
            $progress = $lessonProgressMap->get($lesson->id);

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type,
                'content' => $lesson->content,
                'video_url' => $lesson->video_url,
                'is_preview' => $lesson->is_preview,
                'position' => $lesson->position,
                'created_at' => $lesson->created_at,
                'progress' => [
                    'status' => $progress?->status ?? 'not_started',
                    'completed_at' => $progress?->completed_at,
                    'last_accessed_at' => $progress?->last_accessed_at,
                ],
            ];
        })->values();

        return [
            'course_id' => $dto->courseId,
            'lessons' => $lessonItems,
        ];
    }
}
