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

it('redirects back to the previous page when updating a task', function () {
    $task = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    // Simulate coming from a project page by setting the intended URL in session
    $projectUrl = route('project.show', $newProject);
    
    // First visit the edit page (which should set the intended URL)
    $this->get(route('task.edit', ['task' => $task]), [
        'HTTP_REFERER' => $projectUrl
    ]);
    
    // Then update the task
    $response = $this->patch(route('task.update', ['task' => $task]), [
        'name'        => 'Updated Task Name',
        'description' => 'Updated task description.',
        'project_id'  => $newProject->id,
    ]);

    $response->assertRedirect($projectUrl);
    $response->assertSessionHas('success', 'Task "Updated Task Name" updated successfully.');
});

it('redirects to task index when no intended URL is set', function () {
    $task = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    // Update task without visiting edit page first (no intended URL set)
    $response = $this->patch(route('task.update', ['task' => $task]), [
        'name'        => 'Updated Task Name',
        'description' => 'Updated task description.',
        'project_id'  => $newProject->id,
    ]);

    $response->assertRedirect(route('task.index'));
    $response->assertSessionHas('success', 'Task "Updated Task Name" updated successfully.');
});

it('sets intended URL in session when visiting edit page', function () {
    $task = Task::factory()->create();
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('task.edit', ['task' => $task]), [
        'HTTP_REFERER' => route('project.show', $project)
    ]);

    $response->assertSuccessful();
    $this->assertEquals(route('project.show', $project), session('url.intended'));
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
