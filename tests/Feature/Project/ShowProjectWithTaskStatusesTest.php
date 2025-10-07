<?php

use App\Models\Project;
use App\Models\TaskStatus;

it('includes task statuses with task counts', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);

    $status1 = TaskStatus::factory()->create([
        'name'       => 'To Do',
        'project_id' => $project->id,
        'sort_order' => 1,
    ]);

    $status2 = TaskStatus::factory()->create([
        'name'       => 'Done',
        'project_id' => $project->id,
        'sort_order' => 2,
    ]);

    $response = $this->get(route('project.show', $project));

    $response->assertStatus(200);

    // Verify project data is loaded
    $response->assertHasProp('project');
    $project_data = $response->props('project');

    expect($project_data['id'])->toBe($project->id);
    expect($project_data['name'])->toBe('Test Project');

    // Verify task statuses are loaded
    expect($project_data)->toHaveKey('task_statuses');
    $taskStatuses = $project_data['task_statuses'];

    expect($taskStatuses)->toHaveCount(2);

    // Verify statuses are ordered by sort_order
    expect($taskStatuses[0]['name'])->toBe('To Do');
    expect($taskStatuses[1]['name'])->toBe('Done');

    // Verify task counts are included
    expect($taskStatuses[0])->toHaveKey('tasks_count');
    expect($taskStatuses[1])->toHaveKey('tasks_count');
});

it('works with no task statuses', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Empty Project']);

    $response = $this->get(route('project.show', $project));

    $response->assertStatus(200);

    $project_data = $response->props('project');
    expect($project_data)->toHaveKey('task_statuses');
    expect($project_data['task_statuses'])->toBeEmpty();
});
