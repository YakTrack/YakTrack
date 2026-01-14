<?php

use App\Models\Project;
use App\Models\Task;

it('returns null when project has no task code prefix', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => null,
    ]);

    expect($project->getNextTaskCode())->toBeNull();
});

it('returns first task code when project has no tasks', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0001: ');
});

it('returns next task code when project has existing tasks', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0001: First task',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0002: Second task',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0003: ');
});

it('returns next task code when tasks are not sequential', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0001: First task',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0005: Fifth task',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0006: ');
});

it('ignores tasks without proper code format', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0001: First task',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Random task name',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Another task',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0002: ');
});

it('handles tasks with different prefixes', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0001: First task',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'EFGH-0010: Task from different project',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0002: ');
});

it('formats task code with leading zeros', function () {
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'ABCD-0123: Task',
    ]);

    expect($project->getNextTaskCode())->toBe('ABCD-0124: ');
});
