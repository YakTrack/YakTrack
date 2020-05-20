<?php

use App\Models\Session;
use App\Models\Task;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Session::class, function (Faker $faker) {
    return [
        'started_at' => $startedAt = Carbon::instance($faker->dateTimeThisYear()),
        'ended_at'   => (clone $startedAt)->addSeconds($faker->randomNumber(4)),
    ];
});

$factory->state(Session::class, 'is_running', function () {
    return [
        'ended_at' => null,
    ];
});

$factory->afterCreatingState(Session::class, 'classified', function ($session) {
    $task = Task::inRandomOrder()->first();

    $session->update([
        'task_id'    => $task->id,
        'sprint_id'  => $task->project->client->sprints->random()->id,
        'invoice_id' => $task->project->client->invoices->random()->id,
    ]);
});
