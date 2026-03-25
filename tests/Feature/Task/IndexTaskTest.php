<?php

use App\Models\Task;

it('can see a list of tasks', function () {
    $task = Task::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('task.index'));

    $response->assertSuccessful();

    $response->assertSee($task->name);
    $response->assertSee($task->project->name);
});
