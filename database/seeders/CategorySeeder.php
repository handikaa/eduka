<?php

namespace Database\Seeders;

use App\Infrastructure\Persistance\Eloquent\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table with IT bootcamp-related categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Backend Development',
                'slug' => 'backend-development',
            ],
            [
                'name' => 'Frontend Development',
                'slug' => 'frontend-development',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
            ],
            [
                'name' => 'Database Design',
                'slug' => 'database-design',
            ],
            [
                'name' => 'DevOps & Docker',
                'slug' => 'devops-docker',
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
            ],
            [
                'name' => 'Cloud Computing',
                'slug' => 'cloud-computing',
            ],
            [
                'name' => 'API Development',
                'slug' => 'api-development',
            ],
            [
                'name' => 'JavaScript Fundamentals',
                'slug' => 'javascript-fundamentals',
            ],
            [
                'name' => 'Python Programming',
                'slug' => 'python-programming',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
