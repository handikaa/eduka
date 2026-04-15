<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Infrastructure\Persistance\Eloquent\Models\Category;
use Illuminate\Database\Seeder;

class CourseCategoritiSeeder extends Seeder
{
    /**
     * Seed the course_categories pivot table.
     * Each course will have 2-4 relevant categories assigned.
     */
    public function run(): void
    {
        $courses = Course::all();
        $categories = Category::all();

        if ($courses->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No courses or categories found. Skipping course categories seeding.');
            return;
        }

        // Define category mapping for each course based on course title/content
        $categoryMappings = [
            'PHP Laravel Mastery' => ['Backend Development', 'Web Development', 'API Development'],
            'Advanced JavaScript ES6+' => ['Frontend Development', 'JavaScript Fundamentals', 'Web Development'],
            'React.js Complete Guide' => ['Frontend Development', 'JavaScript Fundamentals', 'Web Development'],
            'Vue.js From Zero to Hero' => ['Frontend Development', 'JavaScript Fundamentals', 'Web Development'],
            'Python Backend Development' => ['Backend Development', 'Python Programming', 'API Development'],
            'Docker & Kubernetes Essentials' => ['DevOps & Docker', 'Cloud Computing', 'Backend Development'],
            'AWS Cloud Fundamentals' => ['Cloud Computing', 'DevOps & Docker'],
            'SQL Database Optimization' => ['Database Design', 'Backend Development'],
            'RESTful API Design' => ['API Development', 'Backend Development', 'Web Development'],
            'GraphQL for Beginners' => ['API Development', 'Backend Development', 'JavaScript Fundamentals'],
            'TypeScript Advanced Patterns' => ['Frontend Development', 'JavaScript Fundamentals', 'Backend Development'],
            'Next.js Full Stack Development' => ['Frontend Development', 'Backend Development', 'JavaScript Fundamentals', 'Web Development'],
            'Angular Enterprise Applications' => ['Frontend Development', 'JavaScript Fundamentals', 'Web Development'],
            'Microservices Architecture' => ['Backend Development', 'DevOps & Docker', 'Cloud Computing', 'API Development'],
            'Test-Driven Development' => ['Backend Development', 'Frontend Development', 'Web Development'],
            'Git & GitHub Mastery' => ['DevOps & Docker', 'Backend Development', 'Frontend Development'],
            'MongoDB NoSQL Database' => ['Database Design', 'Backend Development', 'API Development'],
            'Web Performance Optimization' => ['Frontend Development', 'Web Development', 'Backend Development'],
            'Cybersecurity Fundamentals' => ['Backend Development', 'DevOps & Docker', 'Cloud Computing'],
            'Mobile App Development with Flutter' => ['Mobile Development', 'Backend Development'],
        ];

        $assignedCount = 0;

        foreach ($courses as $course) {
            // Get categories for this course
            $categoryNames = $categoryMappings[$course->title] ?? [];

            if (empty($categoryNames)) {
                // If no specific mapping, randomly select 2-4 categories
                $categoryNames = $categories
                    ->random(rand(2, 4))
                    ->pluck('name')
                    ->toArray();
            }

            // Find categories by name and sync with course
            $categoryIds = $categories
                ->whereIn('name', $categoryNames)
                ->pluck('id')
                ->toArray();

            if (!empty($categoryIds)) {
                $course->categories()->sync($categoryIds);
                $assignedCount += count($categoryIds);

                $this->command->info("Course '{$course->title}' assigned to " . count($categoryIds) . " categories");
            }
        }

        $this->command->info("$assignedCount course-category relationships seeded successfully!");
    }
}
