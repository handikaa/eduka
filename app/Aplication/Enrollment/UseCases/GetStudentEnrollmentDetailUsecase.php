<?php

namespace App\Aplication\Enrollment\UseCases;

use App\Aplication\Enrollment\DTOs\GetStudentEnrollmentDetailDto;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\Enrollment\Exceptions\EnrollmentNotFoundException;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Domain\Enrollment\Exceptions\StudentCannotAccessOtherEnrollmentException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetStudentEnrollmentDetailUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected LessonRepositoryInterface $lessonRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
    ) {}

    public function execute(User $student, GetStudentEnrollmentDetailDto $dto): array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessEnrollmentException();
        }

        $enrollment = $this->enrollmentRepository->findById($dto->enrollmentId);

        if (! $enrollment) {
            throw new EnrollmentNotFoundException();
        }

        if ((int) $enrollment->user_id !== (int) $student->id) {
            throw new StudentCannotAccessOtherEnrollmentException();
        }

        $course = $enrollment->course;

        $lessons = $this->lessonRepository->getByCourseId($course->id);
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

        $totalLessons = $lessonItems->count();
        $completedLessons = $lessonItems->filter(
            fn($lesson) => $lesson['progress']['status'] === 'completed'
        )->count();

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 0;

        return [
            'enrollment' => [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'enrolled_at' => $enrollment->enrolled_at,
                'completed_at' => $enrollment->completed_at,
            ],
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'level' => $course->level,
                'price' => $course->price,
                'thumbnail_url' => $course->thumbnail_url,
                'status' => $course->status,
                'created_at' => $course->created_at,
            ],
            'lessons' => $lessonItems,
            'progress_summary' => [
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress_percentage' => $progressPercentage,
            ],
        ];
    }
}
