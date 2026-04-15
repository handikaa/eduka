<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the courses table with 20 courses from various instructors.
     */
    public function run(): void
    {
        // Get all instructors
        $instructors = User::where('role', 'instructor')->get();

        if ($instructors->isEmpty()) {
            $this->command->warn('No instructors found. Skipping course seeding.');
            return;
        }

        $courseTitles = [
            'PHP Laravel Mastery',
            'Advanced JavaScript ES6+',
            'React.js Complete Guide',
            'Vue.js From Zero to Hero',
            'Python Backend Development',
            'Docker & Kubernetes Essentials',
            'AWS Cloud Fundamentals',
            'SQL Database Optimization',
            'RESTful API Design',
            'GraphQL for Beginners',
            'TypeScript Advanced Patterns',
            'Next.js Full Stack Development',
            'Angular Enterprise Applications',
            'Microservices Architecture',
            'Test-Driven Development',
            'Git & GitHub Mastery',
            'MongoDB NoSQL Database',
            'Web Performance Optimization',
            'Cybersecurity Fundamentals',
            'Mobile App Development with Flutter',
        ];

        $descriptions = [
            'Master the PHP Laravel framework with real-world projects and best practices.',
            'Learn modern JavaScript with ES6+ features, async/await, and functional programming.',
            'Build scalable React applications with hooks, context API, and state management.',
            'Create interactive UI with Vue.js including Vuex and Vue Router.',
            'Develop powerful backend applications using Python and Django/FastAPI.',
            'Containerize your applications with Docker and orchestrate with Kubernetes.',
            'Get started with AWS cloud services and cloud architecture.',
            'Optimize your SQL queries and design efficient databases.',
            'Design and build RESTful APIs with best practices.',
            'Learn GraphQL as an alternative to REST for modern APIs.',
            'Master TypeScript for type-safe JavaScript development.',
            'Build full-stack applications with Next.js framework.',
            'Develop enterprise-grade Angular applications.',
            'Design and implement microservices architecture.',
            'Learn Test-Driven Development practices and testing frameworks.',
            'Master Git version control and GitHub collaboration.',
            'Work with MongoDB and document-based databases.',
            'Improve web performance, speed, and user experience.',
            'Introduction to cybersecurity and security best practices.',
            'Build cross-platform mobile apps with Flutter.',
        ];

        $levels = ['beginner', 'intermediate', 'advanced'];
        $statuses = ['published', 'published', 'draft'];

        foreach ($courseTitles as $index => $title) {
            $instructor = $instructors[$index % count($instructors)];

            Course::create([
                'instructor_id' => $instructor->id,
                'title' => $title,
                'slug' => str($title)->slug(),
                'description' => $descriptions[$index],
                'level' => $levels[$index % count($levels)],
                'price' => (($index + 1) * 75000),
                'thumbnail_url' => 'https://via.placeholder.com/400x300?text=' . urlencode($title),
                'status' => $statuses[$index % count($statuses)],
                'quota' => (($index % 7) + 1) * 10,
                'enrolled_count' => 0,
                'rating_count' => 0,
                'rating_avg' => 0.00,
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('20 courses seeded successfully!');
    }
}
