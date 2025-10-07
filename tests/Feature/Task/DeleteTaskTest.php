<?php

use App\Models\Task;

it('can delete a task', function () {
    $task = Task::factory()->create(['name' => 'Test Task']);

    $this->actingAsUser();

    $response = $this->delete(route('task.destroy', [
        'task' => $task,
    ]));

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});
