<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeThisYear());

        return [
            'started_at' => $startedAt,
            'ended_at'   => (clone $startedAt)->addSeconds($this->faker->randomNumber(4)),
        ];
    }

    /**
     * Indicate that the session is currently running.
     */
    public function running(): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the session is billable.
     */
    public function billable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_billable' => 1,
        ]);
    }

    /**
     * Indicate that the session should be classified with task/sprint/invoice.
     */
    public function classified(): static
    {
        return $this->afterCreating(function (Session $session) {
            $task = Task::inRandomOrder()->first();

            if ($task && $task->project && $task->project->client) {
                $session->update([
                    'task_id'    => $task->id,
                    'sprint_id'  => $task->project->client->sprints->random()?->id,
                    'invoice_id' => $task->project->client->invoices->random()?->id,
                ]);
            }
        });
    }
}
