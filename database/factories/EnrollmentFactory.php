<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
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
            'course_id' => Course::factory(),
            'status' => 'active',
            'enrolled_at' => $this->faker->dateTimeThisMonth(),
            'completed_at' => null,
        ];
    }

    /**
     * State: Enrollment is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
            'completed_at' => null,
        ]);
    }

    /**
     * State: Enrollment has been completed.
     */
    public function completed(): static
    {
        $completedAt = $this->faker->dateTimeThisMonth();

        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'completed_at' => $completedAt,
        ]);
    }

    /**
     * State: Enrollment has been cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
            'completed_at' => null,
        ]);
    }

    /**
     * State: For a specific student (user).
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: For a specific course.
     */
    public function forCourse(Course $course): static
    {
        return $this->state(fn(array $attributes) => [
            'course_id' => $course->id,
        ]);
    }

    /**
     * State: With a specific enrollment date.
     */
    public function enrolledAt($date): static
    {
        return $this->state(fn(array $attributes) => [
            'enrolled_at' => $date,
        ]);
    }
}
