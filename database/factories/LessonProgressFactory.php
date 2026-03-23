<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'status' => 'not_started',
            'completed_at' => null,
            'last_accessed_at' => null,
        ];
    }

    /**
     * State: Lesson has been started but not completed.
     */
    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'in_progress',
            'last_accessed_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }

    /**
     * State: Lesson has been completed.
     */
    public function completed(): static
    {
        $completedAt = $this->faker->dateTimeThisMonth();

        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'completed_at' => $completedAt,
            'last_accessed_at' => $completedAt,
        ]);
    }

    /**
     * State: Lesson has not been started yet.
     */
    public function notStarted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'not_started',
            'completed_at' => null,
            'last_accessed_at' => null,
        ]);
    }

    /**
     * State: For a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: For a specific lesson.
     */
    public function forLesson(Lesson $lesson): static
    {
        return $this->state(fn(array $attributes) => [
            'lesson_id' => $lesson->id,
        ]);
    }
}
