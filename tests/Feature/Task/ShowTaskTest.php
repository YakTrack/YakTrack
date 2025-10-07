<?php

use App\Models\Session;
use App\Models\Task;

it('can view the show task page for a task', function () {
    $this->withoutExceptionHandling();

    $task = Task::factory()->create();

    $session = Session::factory()->create([
        'task_id' => $task->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('task.show', ['task' => $task]));

    $response->assertSuccessful();

    $response->assertSee($task->name);
    $response->assertSee($task->description);

    $response->assertSee($session->created_at);
});
