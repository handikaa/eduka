<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Infrastructure\Persistance\Eloquent\Models\Enrollment;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Seed the enrollments table with student enrollments.
     * Each student enrolls in 2-4 random courses.
     * Some courses will have multiple students, and some will have no enrollments.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('No students or courses found. Skipping enrollment seeding.');
            return;
        }

        $enrollmentCount = 0;
        $statuses = ['active', 'completed', 'cancelled'];

        // Each student enrolls in 2-4 random courses
        foreach ($students as $student) {
            $courseCount = rand(2, 4);
            $randomCourses = $courses->random($courseCount);

            foreach ($randomCourses as $course) {
                // Skip if enrollment already exists (due to UNIQUE constraint)
                if (Enrollment::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists()
                ) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];
                $enrolledAt = now()->subDays(rand(1, 90));

                $enrollmentData = [
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => $status,
                    'enrolled_at' => $enrolledAt,
                    'completed_at' => ($status === 'completed')
                        ? $enrolledAt->addDays(rand(7, 60))
                        : null,
                ];

                Enrollment::create($enrollmentData);
                $enrollmentCount++;

                // Update course enrolled_count
                if ($status === 'active' || $status === 'completed') {
                    $course->increment('enrolled_count');
                }
            }
        }

        $this->command->info("$enrollmentCount enrollments seeded successfully!");
        $this->command->info("Some courses may have no enrollments - this is intentional for realistic data.");
    }
}
