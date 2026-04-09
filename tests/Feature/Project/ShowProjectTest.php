<?php

use App\Models\Project;
use App\Models\Session;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TaskStatus;

it('can view details of a project', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.show', ['project' => $project]));

    $response->assertSuccessful();

    // escape the apostrophe in the same manner as blade is
    $response->assertSee($project->name);

    $response->assertSee($project->description);
    $response->assertSee(e($project->client->name));
});

it('can view details of a project with an apostrophe in the name', function () {
    $project = Project::factory()->create(['name' => 'Steve\'s Test Project']);

    $this->actingAsUser();

    $response = $this->get(route('project.show', ['project' => $project]));

    $response->assertSuccessful();

    // escape the apostrophe in the same manner as blade is
    $response->assertSee($project->name);

    $response->assertSee($project->description);
    $response->assertSee($project->client->name);
});

it('includes paginated tasks data', function () {
    $project = Project::factory()->create();

    // Create some tasks for the project
    $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();

    // Assert that tasks data is passed to Inertia
    $response->assertHasProp('tasks');
    $response->assertHasProp('tasks.data');
    $response->assertPropCount('tasks.data', 3);

    // Assert pagination structure
    $response->assertHasProp('tasks.current_page');
    $response->assertHasProp('tasks.per_page');
    $response->assertHasProp('tasks.total');
    $response->assertPropValue('tasks.total', 3);
    $response->assertPropValue('tasks.per_page', 15);
});

it('displays task names with proper structure', function () {
    $project = Project::factory()->create();
    $taskStatus = TaskStatus::factory()->create(['project_id' => $project->id]);
    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Example Task',
        'status_id'  => $taskStatus->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();

    $taskData = $response->props()['tasks']['data'];

    $row = collect($taskData)->firstWhere('name', 'Example Task');
    expect($row)->not->toBeNull();
    expect($row['name'])->toBe('Example Task');
    expect($row)->toHaveKey('task_status');
    expect($row['task_status']['name'])->toBe($taskStatus->name);
});

it('handles empty tasks list', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    $response->assertHasProp('tasks');
    $response->assertPropValue('tasks.total', 0);
    $response->assertPropCount('tasks.data', 0);
});

it('paginates tasks correctly', function () {
    $project = Project::factory()->create();

    // Create more than one page worth of tasks (15 per page)
    $tasks = Task::factory()->count(20)->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $tasksTab = route('project.show', $project).'?tab=tasks';

    // Test first page
    $response = $this->get($tasksTab);
    $response->assertSuccessful();
    $response->assertPropValue('tasks.current_page', 1);
    $response->assertPropValue('tasks.total', 20);
    $response->assertPropCount('tasks.data', 15);

    // Test second page with tasks_page parameter
    $response = $this->get($tasksTab.'&tasks_page=2');
    $response->assertSuccessful();
    $response->assertPropValue('tasks.current_page', 2);
    $response->assertPropValue('tasks.total', 20);
    $response->assertPropCount('tasks.data', 5);
});

it('orders tasks by name', function () {
    $project = Project::factory()->create();

    // Create tasks with specific names to test ordering
    Task::factory()->create(['project_id' => $project->id, 'name' => 'Z Task']);
    Task::factory()->create(['project_id' => $project->id, 'name' => 'A Task']);
    Task::factory()->create(['project_id' => $project->id, 'name' => 'M Task']);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();

    $taskData = $response->props()['tasks']['data'];
    $taskNames = collect($taskData)->pluck('name')->toArray();

    expect($taskNames)->toBe(['A Task', 'M Task', 'Z Task']);
});

it('only includes tasks for specific project', function () {
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $project1Tasks = Task::factory()->count(2)->create(['project_id' => $project1->id]);
    $project2Tasks = Task::factory()->count(3)->create(['project_id' => $project2->id]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project1));

    $response->assertSuccessful();
    $response->assertPropValue('tasks.total', 2);

    $taskData = $response->props()['tasks']['data'];
    foreach ($taskData as $task) {
        expect($task['project_id'])->toBe($project1->id);
    }
});

it('includes task status with color', function () {
    $project = Project::factory()->create();
    $taskStatus = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'color'      => '#ff6b6b',
    ]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $taskStatus->id,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();

    $taskData = $response->props()['tasks']['data'][0];
    expect($taskData)->toHaveKey('task_status');
    expect($taskData['task_status']['name'])->toBe('In Progress');
    expect($taskData['task_status']['color'])->toBe('#ff6b6b');
});

it('handles tasks without status', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => null,
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();

    $taskData = $response->props()['tasks']['data'][0];
    expect($taskData['task_status'])->toBeNull();
});

it('defaults to overview tab and loads sessions data', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    expect($response->props()['tab'])->toBe('overview');
    $response->assertHasProp('sessions');
    $response->assertHasProp('sessions.data');
    expect($response->props()['sessionsTable']['sort'])->toBe('ended_at');
});

it('loads paginated sessions on the overview tab', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    Session::factory()->count(3)->create([
        'task_id'    => $task->id,
        'started_at' => now()->subHour(),
        'ended_at'   => now(),
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project).'?tab=overview');

    $response->assertSuccessful();
    expect($response->props()['tab'])->toBe('overview');
    $response->assertHasProp('sessions');
    $response->assertHasProp('sessions.data');
    expect($response->props()['sessions']['total'])->toBe(3);
    expect($response->props()['sessionsTable']['sort'])->toBe('ended_at');
});

it('filters sessions by no sprint on the overview tab', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    Session::factory()->create([
        'task_id'    => $task->id,
        'sprint_id'  => null,
        'started_at' => now()->subHour(),
        'ended_at'   => now(),
    ]);
    Session::factory()->create([
        'task_id'    => $task->id,
        'sprint_id'  => Sprint::factory()->create()->id,
        'started_at' => now()->subHour(),
        'ended_at'   => now(),
    ]);

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project).'?tab=overview&sprint_id=none');

    $response->assertSuccessful();
    expect($response->props()['sessions']['total'])->toBe(1);
});
