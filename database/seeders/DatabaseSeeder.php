<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call(CategorySeeder::class);

        // Create 20 student users
        User::factory(20)->create([
            'role' => 'student',
        ]);

        // Create 7 instructor users
        User::factory(7)->instructor()->create();

        // Seed courses
        $this->call(CourseSeeder::class);

        // Assign categories to courses (many-to-many relationships)
        $this->call(CourseCategoritiSeeder::class);

        // Seed lessons for each course
        $this->call(LessonSeeder::class);

        // Seed enrollments (students buying courses)
        $this->call(EnrollmentSeeder::class);

        // Seed lesson progress for enrolled students
        $this->call(LessonProgressSeeder::class);

        // Seed course reviews from students
        $this->call(CourseReviewSeeder::class);
    }
}
