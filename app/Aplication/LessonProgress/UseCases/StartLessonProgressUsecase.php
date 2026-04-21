<?php

namespace App\Aplication\LessonProgress\UseCases;

use App\Aplication\Enrollment\DTOs\CheckStudentEnrollmentAccessDto;
use App\Aplication\Enrollment\UseCases\CheckStudentEnrollmentAccessUsecase;
use App\Aplication\LessonProgress\DTOs\StartLessonProgressDto;
use App\Domain\Lessons\Exceptions\LessonNotFoundException;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\LessonProgress\Exceptions\OnlyStudentCanStartLessonProgressException;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class StartLessonProgressUsecase
{
    public function __construct(
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
        protected LessonRepositoryInterface $lessonRepository,
        protected CheckStudentEnrollmentAccessUsecase $checkStudentEnrollmentAccessUsecase,
    ) {}

    public function execute(User $student, StartLessonProgressDto $dto): LessonProgress
    {
        return DB::transaction(function () use ($student, $dto) {
            if ($student->role !== 'student') {
                throw new OnlyStudentCanStartLessonProgressException();
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
                throw new DomainException($access['reason'] ?? 'Student tidak memiliki akses ke course ini.');
            }

            $lessonProgress = $this->lessonProgressRepository->findByStudentAndLesson(
                studentId: $student->id,
                lessonId: $dto->lessonId
            );

            if (! $lessonProgress) {
                return $this->lessonProgressRepository->create([
                    'user_id' => $student->id,
                    'lesson_id' => $dto->lessonId,
                    'status' => 'in_progress',
                    'completed_at' => null,
                    'last_accessed_at' => now(),
                ]);
            }

            if ($lessonProgress->status === 'not_started') {
                return $this->lessonProgressRepository->update($lessonProgress, [
                    'status' => 'in_progress',
                    'last_accessed_at' => now(),
                ]);
            }

            return $this->lessonProgressRepository->update($lessonProgress, [
                'last_accessed_at' => now(),
            ]);
        });
    }
}
