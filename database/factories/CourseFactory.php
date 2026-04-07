<?php

namespace Database\Factories;

use App\Infrastructure\Persistance\Eloquent\Models\Course;
use App\Infrastructure\Persistance\Eloquent\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistance\Eloquent\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $level = $this->faker->randomElement(['beginner', 'intermediate', 'advanced']);

        return [
            'instructor_id' => User::where('role', 'instructor')->inRandomOrder()->first()?->id ?? User::factory()->instructor(),
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(4),
            'level' => $level,
            'price' => $this->faker->numberBetween(10000, 500000),
            'thumbnail_url' => $this->faker->imageUrl(400, 300, 'course', true),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'quota' => $this->faker->numberBetween(10, 100),
            'enrolled_count' => 0,
            'rating_count' => 0,
            'rating_avg' => 0.00,
            'published_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    /**
     * State: Course for a specific instructor.
     */
    public function forInstructor(User $instructor): static
    {
        return $this->state(fn(array $attributes) => [
            'instructor_id' => $instructor->id,
        ]);
    }

    /**
     * State: Draft course (unpublished).
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * State: Published course.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * State: Beginner level course.
     */
    public function beginner(): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => 'beginner',
        ]);
    }

    /**
     * State: Intermediate level course.
     */
    public function intermediate(): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => 'intermediate',
        ]);
    }

    /**
     * State: Advanced level course.
     */
    public function advanced(): static
    {
        return $this->state(fn(array $attributes) => [
            'level' => 'advanced',
        ]);
    }
}
