<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

it('paginates tasks on the index', function () {
    Task::factory()->count(20)->create();

    $this->actingAsUser();

    $response = $this->get(route('task.index', ['per_page' => 10]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Task/Index')
        ->has('tasks.data', 10)
        ->where('tasks.per_page', 10)
        ->where('tasks.total', 20));
});

it('filters tasks by search query', function () {
    $match = Task::factory()->create(['name' => 'UniqueTaskSearchXyz']);
    Task::factory()->create(['name' => 'OtherTaskName']);

    $this->actingAsUser();

    $response = $this->get(route('task.index', ['q' => 'UniqueTaskSearch']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('tasks.data', 1)
        ->where('tasks.data.0.id', $match->id));
});

it('filters tasks by project', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $taskA = Task::factory()->create(['project_id' => $projectA->id]);
    Task::factory()->create(['project_id' => $projectB->id]);

    $this->actingAsUser();

    $response = $this->get(route('task.index', ['project_id' => $projectA->id]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('tasks.data', 1)
        ->where('tasks.data.0.id', $taskA->id));
});

it('filters tasks by project and status', function () {
    $project = Project::factory()->create();
    $statusOpen = TaskStatus::factory()->create(['project_id' => $project->id, 'name' => 'Open']);
    $statusDone = TaskStatus::factory()->create(['project_id' => $project->id, 'name' => 'Done']);
    $taskOpen = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $statusOpen->id,
    ]);
    Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $statusDone->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('task.index', [
        'project_id' => $project->id,
        'status_id'  => $statusOpen->id,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('tasks.data', 1)
        ->where('tasks.data.0.id', $taskOpen->id));
});

it('sorts tasks by project name', function () {
    $projectZ = Project::factory()->create(['name' => 'Zebra Project']);
    $projectA = Project::factory()->create(['name' => 'Alpha Project']);
    Task::factory()->create(['project_id' => $projectZ->id, 'name' => 'T1']);
    Task::factory()->create(['project_id' => $projectA->id, 'name' => 'T2']);

    $this->actingAsUser();

    $response = $this->get(route('task.index', [
        'sort'      => 'project',
        'direction' => 'asc',
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->where('tasks.data.0.name', 'T2')
        ->where('tasks.data.1.name', 'T1'));
});

it('rejects invalid per_page', function () {
    $this->actingAsUser();

    $this->from(route('task.index'))
        ->get(route('task.index', ['per_page' => 99]))
        ->assertInvalid(['per_page']);
});

it('rejects status that does not belong to the selected project', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $statusB = TaskStatus::factory()->create(['project_id' => $projectB->id]);

    $this->actingAsUser();

    $this->from(route('task.index'))
        ->get(route('task.index', [
            'project_id' => $projectA->id,
            'status_id'  => $statusB->id,
        ]))
        ->assertInvalid(['status_id']);
});

it('includes table state for the frontend', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['name' => 'Listed Task', 'project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->get(route('task.index', [
        'q'           => 'Listed',
        'project_id'  => $project->id,
        'sort'        => 'name',
        'direction'   => 'asc',
        'per_page'    => 25,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Task/Index')
        ->where('table.filters.q', 'Listed')
        ->where('table.filters.project_id', (string) $project->id)
        ->where('table.sort', 'name')
        ->where('table.direction', 'asc')
        ->where('table.per_page', 25));
});

it('returns statuses when a project filter is applied', function () {
    $project = Project::factory()->create();
    TaskStatus::factory()->create(['project_id' => $project->id]);
    TaskStatus::factory()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $this->get(route('task.index', ['project_id' => $project->id]))
        ->assertInertia(fn ($page) => $page->has('statuses', 2));
});

it('returns no statuses when no project filter is applied', function () {
    Project::factory()->create();

    $this->actingAsUser();

    $this->get(route('task.index'))
        ->assertInertia(fn ($page) => $page->where('statuses', []));
});
