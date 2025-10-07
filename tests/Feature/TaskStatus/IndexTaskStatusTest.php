<?php

use App\Models\Project;
use App\Models\TaskStatus;

it('can list task statuses for a specific project', function () {
    $this->withoutExceptionHandling();
    $this->actingAsUser();

    $project1 = Project::factory()->create(['name' => 'Project One']);
    $project2 = Project::factory()->create(['name' => 'Project Two']);

    $status1 = TaskStatus::factory()->create([
        'name'       => 'To Do',
        'project_id' => $project1->id,
        'sort_order' => 1,
    ]);

    $status2 = TaskStatus::factory()->create([
        'name'       => 'In Progress',
        'project_id' => $project1->id,
        'sort_order' => 2,
    ]);

    // Status for different project
    $status3 = TaskStatus::factory()->create([
        'name'       => 'Done',
        'project_id' => $project2->id,
        'sort_order' => 1,
    ]);

    $response = $this->get(route('task-status.index', ['project_id' => $project1->id]));

    $response->assertStatus(200);

    $response->assertPropCount('taskStatuses', 2); // Only 2 statuses for project1

    $taskStatuses = $response->props('taskStatuses');

    $this->assertArrayMatches([
        [
            'id'         => $status1->id,
            'name'       => 'To Do',
            'project_id' => $project1->id,
            'sort_order' => 1,
        ],
        [
            'id'         => $status2->id,
            'name'       => 'In Progress',
            'project_id' => $project1->id,
            'sort_order' => 2,
        ],
    ], $taskStatuses);

    // Ensure the other project's status is not included
    $foundStatus3 = collect($taskStatuses)->contains('id', $status3->id);
    expect($foundStatus3)->toBeFalse('Status from different project should not be included');
});

it('returns task statuses ordered by sort order', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    TaskStatus::factory()->create([
        'name'       => 'Last',
        'project_id' => $project->id,
        'sort_order' => 3,
    ]);

    TaskStatus::factory()->create([
        'name'       => 'First',
        'project_id' => $project->id,
        'sort_order' => 1,
    ]);

    TaskStatus::factory()->create([
        'name'       => 'Middle',
        'project_id' => $project->id,
        'sort_order' => 2,
    ]);

    $response = $this->get(route('task-status.index', ['project_id' => $project->id]));

    $response->assertStatus(200);

    $taskStatuses = $response->props('taskStatuses');

    expect($taskStatuses[0]['name'])->toBe('First');
    expect($taskStatuses[1]['name'])->toBe('Middle');
    expect($taskStatuses[2]['name'])->toBe('Last');
});

it('can list all task statuses when no project id is provided', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create(['name' => 'Project One']);
    $project2 = Project::factory()->create(['name' => 'Project Two']);

    TaskStatus::factory()->create([
        'name'       => 'Status 1',
        'project_id' => $project1->id,
    ]);

    TaskStatus::factory()->create([
        'name'       => 'Status 2',
        'project_id' => $project2->id,
    ]);

    $response = $this->get(route('task-status.index'));

    $response->assertStatus(200);

    $response->assertPropCount('taskStatuses', 2); // Both statuses should be returned

    $taskStatuses = $response->props('taskStatuses');

    // Should include project relationship
    expect($taskStatuses[0])->toHaveKey('id');
    expect($taskStatuses[0])->toHaveKey('name');
    expect($taskStatuses[0])->toHaveKey('color');
    expect($taskStatuses[0])->toHaveKey('sort_order');
    expect($taskStatuses[0])->toHaveKey('is_default');
    expect($taskStatuses[0])->toHaveKey('is_completed');
    expect($taskStatuses[0])->toHaveKey('project_id');
    expect($taskStatuses[0])->toHaveKey('created_at');
    expect($taskStatuses[0])->toHaveKey('updated_at');
    expect($taskStatuses[0])->toHaveKey('project');
    expect($taskStatuses[0]['project'])->toHaveKey('id');
    expect($taskStatuses[0]['project'])->toHaveKey('name');
});

it('cannot list task statuses when unauthenticated', function () {
    $project = Project::factory()->create();

    TaskStatus::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->get(route('task-status.index'));

    $response->assertStatus(302); // Redirect to login
    $response->assertRedirect(route('login'));
});

it('returns empty result for project with no statuses', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('task-status.index', ['project_id' => $project->id]));

    $response->assertStatus(200);
    $response->assertPropCount('taskStatuses', 0);

    $taskStatuses = $response->props('taskStatuses');
    expect($taskStatuses)->toBe([]);
});

it('returns 404 for nonexistent project id', function () {
    $this->actingAsUser();

    $response = $this->get(route('task-status.index', ['project_id' => 99999]));

    $response->assertStatus(404);
});