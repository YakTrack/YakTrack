<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcceptanceCriteria>
 */
class AcceptanceCriteriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'code' => $this->faker->optional(0.7)->regexify('[A-Z]{2,3}-[0-9]{3}'),
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->optional(0.8)->paragraphs(2, true),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the acceptance criteria is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the acceptance criteria has no code.
     */
    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => null,
        ]);
    }

    /**
     * Indicate that the acceptance criteria has no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}