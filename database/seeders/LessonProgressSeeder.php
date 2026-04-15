<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\LessonProgress;
use Illuminate\Database\Seeder;

class LessonProgressSeeder extends Seeder
{
    /**
     * Seed the lesson_progress table.
     * For each enrollment, create progress records for all lessons in that course.
     * Progress status varies: not_started, in_progress, completed
     */
    public function run(): void
    {
        $enrollments = Enrollment::with(['user', 'course.lessons'])->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('No enrollments found. Skipping lesson progress seeding.');
            return;
        }

        $progressCount = 0;
        $statuses = ['not_started', 'in_progress', 'completed'];

        foreach ($enrollments as $enrollment) {
            $lessons = $enrollment->course->lessons;

            // Skip if no lessons in course
            if ($lessons->isEmpty()) {
                continue;
            }

            foreach ($lessons as $index => $lesson) {
                // Skip if progress already exists
                if (LessonProgress::where('user_id', $enrollment->user_id)
                    ->where('lesson_id', $lesson->id)
                    ->exists()
                ) {
                    continue;
                }

                // Determine status based on lesson index and enrollment status
                // First 2 lessons typically are started, later ones may not be started
                $status = $this->determineStatus($index, $enrollment->status);

                $lastAccessedAt = null;
                $completedAt = null;

                if ($status === 'in_progress') {
                    $lastAccessedAt = now()->subDays(rand(0, 30));
                } elseif ($status === 'completed') {
                    $lastAccessedAt = now()->subDays(rand(5, 60));
                    $completedAt = $lastAccessedAt->subDays(rand(1, 10));
                }

                $progressData = [
                    'user_id' => $enrollment->user_id,
                    'lesson_id' => $lesson->id,
                    'status' => $status,
                    'completed_at' => $completedAt,
                    'last_accessed_at' => $lastAccessedAt,
                ];

                LessonProgress::create($progressData);
                $progressCount++;
            }
        }

        $this->command->info("$progressCount lesson progress records seeded successfully!");
    }

    /**
     * Determine the progress status based on lesson index and enrollment status.
     */
    private function determineStatus(int $lessonIndex, string $enrollmentStatus): string
    {
        // If enrollment is completed, majority of lessons should be completed
        if ($enrollmentStatus === 'completed') {
            $random = rand(1, 100);
            if ($random <= 80) {
                return 'completed'; // 80% completed
            } elseif ($random <= 95) {
                return 'in_progress'; // 15% in progress
            } else {
                return 'not_started'; // 5% not started
            }
        }

        // If enrollment is cancelled, most lessons are not started
        if ($enrollmentStatus === 'cancelled') {
            $random = rand(1, 100);
            if ($random <= 60) {
                return 'not_started'; // 60% not started
            } elseif ($random <= 90) {
                return 'in_progress'; // 30% in progress
            } else {
                return 'completed'; // 10% completed
            }
        }

        // For active enrollment
        // First lessons are usually started
        if ($lessonIndex <= 1) {
            $random = rand(1, 100);
            if ($random <= 70) {
                return 'completed'; // 70% completed
            } else {
                return 'in_progress'; // 30% in progress
            }
        }

        // Middle lessons
        if ($lessonIndex <= 3) {
            $random = rand(1, 100);
            if ($random <= 40) {
                return 'completed'; // 40% completed
            } elseif ($random <= 70) {
                return 'in_progress'; // 30% in progress
            } else {
                return 'not_started'; // 30% not started
            }
        }

        // Later lessons usually not started yet
        $random = rand(1, 100);
        if ($random <= 50) {
            return 'not_started'; // 50% not started
        } elseif ($random <= 75) {
            return 'in_progress'; // 25% in progress
        } else {
            return 'completed'; // 25% completed
        }
    }
}
