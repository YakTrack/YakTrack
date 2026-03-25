<?php

use App\Models\Project;
use App\Models\Task;

it('can load the page to edit a task', function () {
    $task = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('task.edit', ['task' => $task]));

    $response->assertSuccessful();

    $response->assertSee($newProject->name);
});

it('can update a task with a patch request', function () {
    $this->withoutExceptionHandling();

    $task = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->patch(route('task.update', ['task' => $task]), $updatedTaskDetails = [
        'name'        => 'Updated Task Name',
        'description' => 'Updated task description.',
        'project_id'  => $newProject->id,
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', $updatedTaskDetails);
});

it('redirects back to the previous page when updating a task', function () {
    $task = Task::factory()->create();
    $newProject = Project::factory()->create();

    $this->actingAsUser();

    $projectUrl = route('project.show', $newProject);

    $this->get(route('task.edit', ['task' => $task]), [
        'HTTP_REFERER' => $projectUrl,
    ]);

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
        'HTTP_REFERER' => route('project.show', $project),
    ]);

    $response->assertSuccessful();
    $this->assertEquals(route('project.show', $project), session('url.intended'));
});
