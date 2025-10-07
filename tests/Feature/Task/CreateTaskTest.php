<?php

use App\Models\Project;
use App\Models\Task;

it('can view the page to create a task', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $parentTask = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->get(route('task.create'));

    $response->assertSuccessful();

    $response->assertSee($project->name);
    $response->assertSee($parentTask->name);
});

it('can submit a post request to create a task', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();
    $parentTask = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->post(route('task.store'), [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
        'parent_id'   => $parentTask->id,
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
        'parent_id'   => $parentTask->id,
    ]);
});

it('can submit a post request to create a task with required fields only', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();
    $parentTask = Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->post(route('task.store'), [
        'name'        => 'Test Task',
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', [
        'name'        => 'Test Task',
        'description' => '',
        'project_id'  => null,
        'parent_id'   => null,
    ]);
});

it('cannot create a duplicate named task for a project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    Task::factory()->create([
        'name'       => 'Test Task',
        'project_id' => $project->id,
    ]);

    $response = $this->post(route('task.store'), [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('name');

    $this->assertDatabaseMissing('tasks', [
        'description' => 'Test task description.',
    ]);
});
