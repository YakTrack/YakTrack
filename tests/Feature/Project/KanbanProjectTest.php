<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

it('can view kanban board for a project', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', ['project' => $project]));

    $response->assertSuccessful();
    $response->assertSee($project->name);
    $response->assertSee($project->description);
    $response->assertSee(e($project->client->name));
});

it('loads all tasks for the project on the kanban board', function () {
    $project = Project::factory()->create();
    $status = TaskStatus::factory()->create(['project_id' => $project->id]);

    $taskA = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
        'name'       => 'Task A',
    ]);

    $taskB = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
        'name'       => 'Task B',
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();
    $response->assertHasProp('tasks');

    $tasks = $response->props()['tasks'];

    expect($tasks)->toHaveCount(2);
    expect(collect($tasks)->pluck('name')->sort()->values()->all())->toBe(['Task A', 'Task B']);
});

it('loads task statuses with task counts', function () {
    $project = Project::factory()->create();

    $status1 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'To Do',
        'sort_order' => 1,
    ]);

    $status2 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'sort_order' => 2,
    ]);

    Task::factory()->count(2)->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'status_id'  => $status2->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();
    $response->assertHasProp('project.task_statuses');

    $taskStatuses = $response->props()['project']['task_statuses'];

    expect($taskStatuses[0]['tasks_count'])->toBe(2);
    expect($taskStatuses[1]['tasks_count'])->toBe(3);
});

it('orders statuses by sort_order', function () {
    $project = Project::factory()->create();

    TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Done',
        'sort_order' => 3,
    ]);

    TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'To Do',
        'sort_order' => 1,
    ]);

    TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'sort_order' => 2,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();

    $taskStatuses = $response->props()['project']['task_statuses'];
    $statusNames = collect($taskStatuses)->pluck('name')->toArray();

    expect($statusNames)->toBe(['To Do', 'In Progress', 'Done']);
});

it('handles project with no task statuses', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();
    $response->assertHasProp('project.task_statuses');

    $taskStatuses = $response->props()['project']['task_statuses'];
    expect($taskStatuses)->toBeEmpty();
});

it('handles project with no tasks', function () {
    $project = Project::factory()->create();
    TaskStatus::factory()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();
    $response->assertHasProp('tasks');

    $tasks = $response->props()['tasks'];
    expect($tasks)->toBeEmpty();
});

it('only includes tasks for specific project', function () {
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $status1 = TaskStatus::factory()->create(['project_id' => $project1->id]);
    $status2 = TaskStatus::factory()->create(['project_id' => $project2->id]);

    Task::factory()->count(2)->create([
        'project_id' => $project1->id,
        'status_id'  => $status1->id,
    ]);

    Task::factory()->count(3)->create([
        'project_id' => $project2->id,
        'status_id'  => $status2->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project1));

    $response->assertSuccessful();

    $tasks = $response->props()['tasks'];
    expect($tasks)->toHaveCount(2);

    foreach ($tasks as $task) {
        expect($task['project_id'])->toBe($project1->id);
    }
});

it('includes task status relationship in task data', function () {
    $project = Project::factory()->create();
    $taskStatus = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'color'      => '#ff6b6b',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $taskStatus->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();

    $tasks = $response->props()['tasks'];
    expect($tasks[0])->toHaveKey('task_status');
    expect($tasks[0]['task_status']['name'])->toBe('In Progress');
    expect($tasks[0]['task_status']['color'])->toBe('#ff6b6b');
});

it('excludes closed statuses from the kanban board', function () {
    $project = Project::factory()->create();

    $openStatus = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'is_closed'  => false,
    ]);

    $closedStatus = TaskStatus::factory()->closed()->create([
        'project_id' => $project->id,
        'name'       => 'Closed',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $openStatus->id,
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $closedStatus->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.kanban', $project));

    $response->assertSuccessful();
    $response->assertHasProp('project.task_statuses');

    $taskStatuses = $response->props()['project']['task_statuses'];
    $statusIds = collect($taskStatuses)->pluck('id')->toArray();

    expect($statusIds)->toContain($openStatus->id);

    expect($statusIds)->not->toContain($closedStatus->id);
});
