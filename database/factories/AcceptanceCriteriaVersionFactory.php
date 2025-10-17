<?php

namespace Database\Factories;

use App\Models\AcceptanceCriteria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcceptanceCriteriaVersion>
 */
class AcceptanceCriteriaVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'acceptance_criteria_id' => AcceptanceCriteria::factory(),
            'code' => $this->faker->optional(0.7)->regexify('[A-Z]{2,3}-[0-9]{3}'),
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->optional(0.8)->paragraphs(2, true),
            'version_number' => $this->faker->numberBetween(1, 5),
            'changed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'changed_by_user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the version has no code.
     */
    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => null,
        ]);
    }

    /**
     * Indicate that the version has no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}