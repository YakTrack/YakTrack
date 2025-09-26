<?php

namespace Database\Factories;

use App\Models\Target;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Target>
 */
class TargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value_unit'    => collect(Target::VALUE_UNITS)->random()['key'],
            'value'         => $this->faker->numberBetween(0, 100),
            'billable_only' => 0,
        ];
    }

    /**
     * Indicate that the target is for a specific date.
     */
    public function forDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration'      => 1,
            'duration_unit' => Target::DURATION_UNITS['DAYS']['key'],
        ]);
    }

    /**
     * Indicate that the target value is in hours.
     */
    public function inHours(): static
    {
        return $this->state(fn (array $attributes) => [
            'value_unit' => Target::VALUE_UNITS['HOURS']['key'],
        ]);
    }
}