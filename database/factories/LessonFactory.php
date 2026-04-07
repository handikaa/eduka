<?php

namespace Database\Factories;

use App\Infrastructure\Persistance\Eloquent\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistance\Eloquent\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['video', 'text', 'quiz', 'assignment']),
            'content' => fake()->paragraphs(3, true),
            'video_url' => fake()->randomElement([null, 'https://example.com/video.mp4', 'https://example.com/video2.mp4']),
            'file_url' => fake()->randomElement([null, 'https://example.com/file.pdf', 'https://example.com/file.docx']),
            'order_index' => fake()->numberBetween(1, 10),
            'is_preview' => fake()->boolean(20), // 20% chance true
        ];
    }

    /**
     * Indicate that this is a video lesson.
     */
    public function video(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
        ]);
    }

    /**
     * Indicate that this is a text lesson.
     */
    public function text(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'text',
            'video_url' => null,
        ]);
    }

    /**
     * Indicate that this is a quiz lesson.
     */
    public function quiz(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'quiz',
            'video_url' => null,
            'file_url' => null,
        ]);
    }

    /**
     * Indicate that this is an assignment lesson.
     */
    public function assignment(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'assignment',
            'file_url' => 'https://example.com/assignment.pdf',
        ]);
    }

    /**
     * Mark this lesson as preview.
     */
    public function preview(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_preview' => true,
        ]);
    }
}
