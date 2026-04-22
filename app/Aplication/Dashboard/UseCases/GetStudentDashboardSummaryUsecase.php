<?php

namespace App\Aplication\Dashboard\UseCases;

use App\Aplication\Dashboard\DTOs\GetStudentDashboardSummaryDto;
use App\Domain\Dashboard\Exceptions\OnlyStudentCanAccessDashboardException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetStudentDashboardSummaryUsecase
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected LessonProgressRepositoryInterface $lessonProgressRepository,
    ) {}

    public function execute(User $student, GetStudentDashboardSummaryDto $dto): array
    {
        if ($student->role !== 'student') {
            throw new OnlyStudentCanAccessDashboardException();
        }

        $totalEnrolledCourses = $this->enrollmentRepository->countByStudentId($student->id);
        $totalActiveCourses = $this->enrollmentRepository->countByStudentIdAndStatus($student->id, 'active');
        $totalCompletedCourses = $this->enrollmentRepository->countByStudentIdAndStatus($student->id, 'completed');

        $latestAccessedProgress = $this->lessonProgressRepository->findLatestAccessedByStudentId($student->id);

        $continueLearning = null;

        if ($latestAccessedProgress && $latestAccessedProgress->lesson && $latestAccessedProgress->lesson->course) {
            $lesson = $latestAccessedProgress->lesson;
            $course = $lesson->course;

            $continueLearning = [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_slug' => $course->slug,
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'lesson_type' => $lesson->type,
                'last_accessed_at' => $latestAccessedProgress->last_accessed_at,
            ];
        }

        return [
            'total_enrolled_courses' => $totalEnrolledCourses,
            'total_active_courses' => $totalActiveCourses,
            'total_completed_courses' => $totalCompletedCourses,
            'continue_learning' => $continueLearning,
        ];
    }
}
