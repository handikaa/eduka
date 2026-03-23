<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseReviewSeeder extends Seeder
{
    /**
     * Seed the course_reviews table.
     * Only students who have active or completed enrollments can review.
     * Each student reviews 30-50% of their enrolled courses with ratings 1-5.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found. Skipping course review seeding.');
            return;
        }

        $reviewCount = 0;

        foreach ($students as $student) {
            // Get all active/completed enrollments for this student
            $enrollments = Enrollment::where('user_id', $student->id)
                ->whereIn('status', ['active', 'completed'])
                ->get();

            if ($enrollments->isEmpty()) {
                continue;
            }

            // Student reviews 30-50% of their enrolled courses
            $reviewCount += $this->createReviewsForStudent($student, $enrollments);
        }

        $this->command->info("$reviewCount course reviews seeded successfully!");
        $this->command->info("Course ratings have been automatically calculated.");
    }

    /**
     * Create reviews for a student's enrolled courses.
     */
    private function createReviewsForStudent(User $student, $enrollments): int
    {
        $count = 0;
        $reviewPercentage = rand(30, 50); // Student reviews 30-50% of their courses

        foreach ($enrollments as $enrollment) {
            // Random chance to review this course
            if (rand(1, 100) > $reviewPercentage) {
                continue;
            }

            // Check if review already exists
            if (CourseReview::where('user_id', $student->id)
                ->where('course_id', $enrollment->course_id)
                ->exists()
            ) {
                continue;
            }

            // Generate rating and comment based on enrollment status
            $rating = $this->generateRating($enrollment->status);
            $comment = $this->generateComment($rating, $enrollment->course_id);

            $review = CourseReview::create([
                'course_id' => $enrollment->course_id,
                'user_id' => $student->id,
                'rating' => $rating,
                'comment' => $comment,
                'is_delete' => false,
            ]);

            // Update course rating summary
            $this->updateCourseRatings($enrollment->course_id);
            $count++;
        }

        return $count;
    }

    /**
     * Generate rating based on enrollment status.
     */
    private function generateRating(string $enrollmentStatus): int
    {
        if ($enrollmentStatus === 'completed') {
            // Completed students usually give higher ratings (4-5)
            $ratings = [3, 4, 4, 4, 5, 5, 5];
            return $ratings[array_rand($ratings)];
        } else {
            // Active students give varied ratings
            return rand(1, 5);
        }
    }

    /**
     * Generate review comment based on rating.
     */
    private function generateComment(int $rating, int $courseId): string
    {
        $positiveComments = [
            'Excellent course! Learned a lot. Highly recommended!',
            'Great content and well-organized. Instructor is very knowledgeable.',
            'Really enjoyed this course. Great practical examples!',
            'Fantastic! Exactly what I was looking for.',
            'Excellent teaching method and clear explanations.',
            'Best course I\'ve taken. Worth every penny!',
            'Very comprehensive and easy to follow. Highly satisfied!',
            'Outstanding quality. Exceeded my expectations!',
            'Brilliant course with useful real-world projects.',
            'Perfect for beginners and intermediates alike.',
        ];

        $goodComments = [
            'Good course with useful content.',
            'Solid material and good instruction.',
            'Nice course, learned new things.',
            'Pretty good, but could add more examples.',
            'Decent course, worth checking out.',
            'Good content overall.',
            'Satisfactory course with good examples.',
            'Nice lessons and practical knowledge.',
        ];

        $averageComments = [
            'It\'s okay, some good parts but also some confusing sections.',
            'Average course, nothing special.',
            'Some useful content but could be better organized.',
            'Decent but expected more depth.',
            'Mixed feelings about this course.',
            'Some content is good, some not so much.',
            'It\'s fine, but I\'ve seen better.',
        ];

        $poorComments = [
            'Not worth the money. Disappointed.',
            'Poor quality and confusing instructions.',
            'Below expectations unfortunately.',
            'Difficult to follow and incomplete.',
            'Not recommended. Waste of time.',
            'Very disappointed with the content.',
            'Poorly explained and outdated material.',
        ];

        $veryPoorComments = [
            'Terrible course. Do not buy!',
            'One of the worst courses I\'ve taken.',
            'Complete waste of money.',
            'Extremely disappointed. Avoid this!',
            'Horrible content and poor instruction.',
            'Absolutely not recommended.',
            'This is just bad on every level.',
        ];

        return match ($rating) {
            5 => $positiveComments[array_rand($positiveComments)],
            4 => $goodComments[array_rand($goodComments)],
            3 => $averageComments[array_rand($averageComments)],
            2 => $poorComments[array_rand($poorComments)],
            1 => $veryPoorComments[array_rand($veryPoorComments)],
            default => 'No comment',
        };
    }

    /**
     * Update course rating summary (rating_count and rating_avg).
     */
    private function updateCourseRatings(int $courseId): void
    {
        $reviews = CourseReview::where('course_id', $courseId)
            ->where('is_delete', false)
            ->get();

        if ($reviews->isEmpty()) {
            return;
        }

        $ratingCount = $reviews->count();
        $ratingAvg = round($reviews->avg('rating'), 2);

        $course = Course::findOrFail($courseId);
        if ($course) {
            $course->update([
                'rating_count' => $ratingCount,
                'rating_avg' => $ratingAvg,
            ]);
        }
    }
}
