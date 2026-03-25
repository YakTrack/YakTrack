<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

it('assigns multiple tasks to a project', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $statusA = TaskStatus::factory()->default()->create(['project_id' => $projectA->id]);
    $statusB = TaskStatus::factory()->default()->create(['project_id' => $projectB->id]);

    $task1 = Task::factory()->create([
        'project_id' => $projectA->id,
        'status_id'  => $statusA->id,
    ]);
    $task2 = Task::factory()->create([
        'project_id' => $projectA->id,
        'status_id'  => $statusA->id,
    ]);

    $this->actingAsUser();

    $response = $this->patch(route('task.bulk-assign-project'), [
        'project_id' => $projectB->id,
        'task_ids'   => [$task1->id, $task2->id],
    ]);

    $response->assertRedirect(route('task.index'));
    $response->assertSessionHas('success');

    expect($task1->fresh()->project_id)->toBe($projectB->id);
    expect($task2->fresh()->project_id)->toBe($projectB->id);
    expect($task1->fresh()->status_id)->toBe($statusB->id);
    expect($task2->fresh()->status_id)->toBe($statusB->id);
});

it('keeps status when it already belongs to the target project', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $sharedStatus = TaskStatus::factory()->create(['project_id' => $projectB->id]);

    $task = Task::factory()->create([
        'project_id' => $projectA->id,
        'status_id'  => $sharedStatus->id,
    ]);

    $this->actingAsUser();

    $this->patch(route('task.bulk-assign-project'), [
        'project_id' => $projectB->id,
        'task_ids'   => [$task->id],
    ])->assertRedirect(route('task.index'));

    expect($task->fresh()->status_id)->toBe($sharedStatus->id);
});

it('validates required task selection', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $this->patch(route('task.bulk-assign-project'), [
        'project_id' => $project->id,
        'task_ids'   => [],
    ])->assertSessionHasErrors('task_ids');
});
