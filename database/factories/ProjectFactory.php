<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Support\FactoryGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => ucfirst(app(FactoryGenerator::class)->projectName()),
            'client_id'     => Client::factory(),
            'description'   => $this->faker->sentence,
        ];
    }
}