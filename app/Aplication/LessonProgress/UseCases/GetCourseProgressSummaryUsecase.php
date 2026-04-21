<?php

namespace App\Aplication\LessonProgress\UseCases;

use DomainException;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Aplication\Enrollment\UseCases\CheckStudentEnrollmentAccessUsecase;
use App\Domain\Enrollment\Exceptions\OnlyStudentCanAccessEnrollmentException;
use App\Aplication\LessonProgress\DTOs\GetCourseProgressSummaryDto;

class GetCourseProgressSummaryUsecase
{
    public function __construct(
        protected LessonRepositoryInterface $lessonRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
        protected CheckStudentEnrollmentAccessUsecase $checkStudentEnrollmentAccessUsecase,
    ) {}

    public function execute(User $student, GetCourseProgressSummaryDto $dto): array
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

        $totalLessons = $lessons->count();
        $completedLessons = $lessonProgressCollection->where('status', 'completed')->count();
        $inProgressLessons = $lessonProgressCollection->where('status', 'in_progress')->count();

        $notStartedLessons = max($totalLessons - ($completedLessons + $inProgressLessons), 0);

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 0;

        return [
            'course_id' => $dto->courseId,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'in_progress_lessons' => $inProgressLessons,
            'not_started_lessons' => $notStartedLessons,
            'progress_percentage' => $progressPercentage,
        ];
    }
}
