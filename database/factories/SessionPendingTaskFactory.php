<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SessionPendingTask>
 */
class SessionPendingTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_id' => Session::factory(),
            'task_id'    => Task::factory(),
        ];
    }
}
