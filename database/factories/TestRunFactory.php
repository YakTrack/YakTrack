<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestRun>
 */
class TestRunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id'          => Project::factory(),
            'name'                => $this->faker->words(3, true).' Test Run',
            'description'         => $this->faker->optional(0.6)->paragraphs(2, true),
            'executed_at'         => $this->faker->dateTimeBetween('-1 week', 'now'),
            'executed_by_user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the test run has no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Indicate that the test run was executed recently.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'executed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
