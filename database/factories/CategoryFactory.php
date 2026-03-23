<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }

    /**
     * State: Backend category.
     */
    public function backend(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Backend',
            'slug' => 'backend',
        ]);
    }

    /**
     * State: Frontend category.
     */
    public function frontend(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Frontend',
            'slug' => 'frontend',
        ]);
    }

    /**
     * State: Web Development category.
     */
    public function webDevelopment(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Web Development',
            'slug' => 'web-development',
        ]);
    }

    /**
     * State: Mobile Development category.
     */
    public function mobileDevelopment(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mobile Development',
            'slug' => 'mobile-development',
        ]);
    }

    /**
     * State: Database category.
     */
    public function database(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Database',
            'slug' => 'database',
        ]);
    }

    /**
     * State: DevOps category.
     */
    public function devops(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'DevOps',
            'slug' => 'devops',
        ]);
    }

    /**
     * State: UI/UX Design category.
     */
    public function uiux(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
        ]);
    }

    /**
     * State: Data Science category.
     */
    public function dataScience(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Data Science',
            'slug' => 'data-science',
        ]);
    }
}
