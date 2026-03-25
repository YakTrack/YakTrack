<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sprint>
 */
class SprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'    => $this->faker->word(),
            'is_open' => true,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Sprint $sprint) {
            if ($sprint->projects()->count() === 0) {
                $sprint->projects()->attach(Project::factory()->create());
            }
            $first = $sprint->projects()->first();
            if ($first) {
                $sprint->name = implode(' ', [
                    $first->name,
                    '-',
                    'Sprint',
                    ($sprint->id % $sprint->projects()->count()) + 1,
                ]);
                $sprint->save();
            }
        });
    }
}
