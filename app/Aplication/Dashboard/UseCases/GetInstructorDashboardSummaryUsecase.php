<?php

namespace App\Aplication\Dashboard\UseCases;

use App\Aplication\Dashboard\DTOs\GetInstructorDashboardSummaryDto;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Dashboard\Exceptions\OnlyInstructorCanAccessDashboardException;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Domain\CourseReview\Repositories\CourseReviewRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Models\User;

class GetInstructorDashboardSummaryUsecase
{
    public function __construct(
        protected CourseRepositoryInterface $courseRepository,
        protected EnrollmentRepositoryInterface $enrollmentRepository,
        protected CourseReviewRepositoryInterface $courseReviewRepository,
    ) {}

    public function execute(User $instructor, GetInstructorDashboardSummaryDto $dto): array
    {
        if ($instructor->role !== 'instructor') {
            throw new OnlyInstructorCanAccessDashboardException();
        }

        $courseIds = $this->courseRepository->getIdsByInstructorId($instructor->id);

        $totalCourses = $this->courseRepository->countByInstructorId($instructor->id);
        $totalPublishedCourses = $this->courseRepository->countByInstructorIdAndStatus($instructor->id, 'published');
        $totalDraftCourses = $this->courseRepository->countByInstructorIdAndStatus($instructor->id, 'draft');
        $totalArchivedCourses = $this->courseRepository->countByInstructorIdAndStatus($instructor->id, 'archived');

        $totalEnrollments = $this->enrollmentRepository->countByCourseIds($courseIds);
        $totalReviews = $this->courseReviewRepository->countActiveByCourseIds($courseIds);
        $averageRating = $this->courseReviewRepository->getActiveAverageRatingByCourseIds($courseIds);

        return [
            'total_courses' => $totalCourses,
            'total_published_courses' => $totalPublishedCourses,
            'total_draft_courses' => $totalDraftCourses,
            'total_archived_courses' => $totalArchivedCourses,
            'total_enrollments' => $totalEnrollments,
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
        ];
    }
}
