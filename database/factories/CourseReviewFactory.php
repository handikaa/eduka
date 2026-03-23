<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseReview>
 */
class CourseReviewFactory extends Factory
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
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
            'is_delete' => false,
        ];
    }

    /**
     * State: 5-star review (excellent).
     */
    public function fiveStar(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 5,
        ]);
    }

    /**
     * State: 4-star review (good).
     */
    public function fourStar(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 4,
        ]);
    }

    /**
     * State: 3-star review (average).
     */
    public function threeStar(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 3,
        ]);
    }

    /**
     * State: 2-star review (poor).
     */
    public function twoStar(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 2,
        ]);
    }

    /**
     * State: 1-star review (very poor).
     */
    public function oneStar(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 1,
        ]);
    }

    /**
     * State: Review is marked as deleted.
     */
    public function deleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_delete' => true,
        ]);
    }

    /**
     * State: Review is active (not deleted).
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_delete' => false,
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
     * State: By a specific user (student).
     */
    public function byUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: Without comment.
     */
    public function withoutComment(): static
    {
        return $this->state(fn(array $attributes) => [
            'comment' => null,
        ]);
    }
}
