<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThirdPartyApplication>
 */
class ThirdPartyApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    /**
     * Indicate that the third party application is Wrike.
     */
    public function wrike(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'wrike',
        ]);
    }
}
