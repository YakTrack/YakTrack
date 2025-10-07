<?php

use App\Models\Task;

it('can see a list of tasks', function () {
    $parentTask = Task::factory()->create();
    $task = Task::factory()->create(['parent_id' => $parentTask->id]);

    $this->actingAsUser();

    $response = $this->get(route('task.index'));

    $response->assertSuccessful();

    $response->assertSee($task->name);
    $response->assertSee($task->parent->shortName);
    $response->assertSee($task->project->name);
});
