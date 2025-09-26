<?php

namespace Database\Factories;

use App\Models\Project;
use App\Support\FactoryGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => ucfirst(app(FactoryGenerator::class)->taskName()),
            'description'   => $this->faker->paragraph,
            'status'        => $this->faker->word,
            'project_id'    => Project::factory(),
        ];
    }
}
