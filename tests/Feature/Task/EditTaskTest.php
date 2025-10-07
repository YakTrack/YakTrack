<?php

use App\Models\Project;
use App\Models\Task;

it('can load the page to edit a task', function () {
    $task = Task::factory()->create();
    $newParentTask = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('task.edit', ['task' => $task]));

    $response->assertSuccessful();

    $response->assertSee($newProject->name);
    $response->assertSee($newParentTask->name);
});

it('can update a task with a patch request', function () {
    $this->withoutExceptionHandling();

    $task = Task::factory()->create();
    $newParentTask = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('task.update', ['task' => $task]), $updatedTaskDetails = [
        'name'        => 'Updated Task Name',
        'description' => 'Updated task description.',
        'parent_id'   => $newParentTask->id,
        'project_id'  => $newProject->id,
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', $updatedTaskDetails);
});

it('can remove a parent task from a task with a patch request', function () {
    $this->withoutExceptionHandling();

    $existingParentTask = Task::factory()->create();
    $task = Task::factory()->create([
        'parent_id' => $existingParentTask->id,
    ]);

    $this->actingAsUser();

    $response = $this->patch(route('task.update', ['task' => $task]), $updatedTaskDetails = [
        'name'        => 'Updated Task Name',
        'description' => 'Updated task description.',
        'parent_id'   => null,
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', $updatedTaskDetails);
});
