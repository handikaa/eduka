<?php

namespace App\Aplication\LessonProgress\UseCases;

use DomainException;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\Lessons\Exceptions\LessonNotFoundException;
use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Aplication\Enrollment\DTOs\CompleteEnrollmentDto;
use App\Aplication\Enrollment\UseCases\CompleteEnrollmentUsecase;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Aplication\Enrollment\UseCases\CheckStudentEnrollmentAccessUsecase;
use App\Domain\User\Exceptions\OnlyStudentCanCompleteLessonProgressException;
use App\Aplication\LessonProgress\DTOs\MarkLessonProgressAsCompletedDto;

class MarkLessonProgressAsCompletedUsecase
{
    public function __construct(
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
        protected LessonRepositoryInterface $lessonRepository,
        protected CheckStudentEnrollmentAccessUsecase $checkStudentEnrollmentAccessUsecase,
        protected CompleteEnrollmentUsecase $completeEnrollmentUsecase,
    ) {}

    public function execute(User $student, MarkLessonProgressAsCompletedDto $dto): LessonProgress
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanCompleteLessonProgressException();
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

            if (! $lessonProgress) {
                $lessonProgress = $this->lessonProgressRepository->create([
                    'user_id' => $student->id,
                    'lesson_id' => $dto->lessonId,
                    'status' => 'completed',
                    'completed_at' => now(),
                    'last_accessed_at' => now(),
                ]);
            } else {
                $lessonProgress = $this->lessonProgressRepository->update($lessonProgress, [
                    'status' => 'completed',
                    'completed_at' => now(),
                    'last_accessed_at' => now(),
                ]);
            }

            $totalLessons = $this->lessonRepository->countByCourseId($lesson->course_id);
            $completedLessons = $this->lessonProgressRepository->countCompletedByStudentAndCourse(
                studentId: $student->id,
                courseId: $lesson->course_id
            );

            if ($totalLessons > 0 && $completedLessons >= $totalLessons) {
                $enrollment = $access['enrollment'] ?? null;

                if ($enrollment && $enrollment->status !== 'completed') {
                    $this->completeEnrollmentUsecase->execute(
                        student: $student,
                        dto: new CompleteEnrollmentDto(
                            courseId: $lesson->course_id
                        )
                    );
                }
            }

            return $lessonProgress;
        });
    }
}
