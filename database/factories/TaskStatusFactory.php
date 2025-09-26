<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskStatus>
 */
class TaskStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => $this->faker->words(2, true),
            'color'        => $this->faker->hexColor(),
            'sort_order'   => $this->faker->numberBetween(0, 10),
            'is_default'   => false,
            'is_completed' => false,
            'project_id'   => Project::factory(),
        ];
    }

    /**
     * Indicate that the task status is the default status.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that the task status represents completed tasks.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }
}
