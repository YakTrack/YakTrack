<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

it('can update task status via patch request', function () {
    $project = Project::factory()->create();
    $status1 = TaskStatus::factory()->create(['project_id' => $project->id, 'name' => 'To Do']);
    $status2 = TaskStatus::factory()->create(['project_id' => $project->id, 'name' => 'In Progress']);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    $this->actingAsUser();

    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'status_id' => $status2->id,
    ]);
});

it('returns updated task data with task status relationship', function () {
    $project = Project::factory()->create();
    $status1 = TaskStatus::factory()->create(['project_id' => $project->id]);
    $status2 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Done',
        'color'      => '#00ff00',
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    $this->actingAsUser();

    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'task' => [
            'id',
            'name',
            'status_id',
            'task_status' => [
                'id',
                'name',
                'color',
            ],
        ],
    ]);

    $responseData = $response->json();
    expect($responseData['task']['task_status']['name'])->toBe('Done');
    expect($responseData['task']['task_status']['color'])->toBe('#00ff00');
});

it('requires status_id to be provided', function () {
    $project = Project::factory()->create();
    $status = TaskStatus::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
    ]);

    $this->actingAsUser();

    $response = $this->patchJson(route('task.updateStatus', $task), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status_id']);
});

it('validates status_id exists in task_statuses table', function () {
    $project = Project::factory()->create();
    $status = TaskStatus::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
    ]);

    $this->actingAsUser();

    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => 99999, // Non-existent status
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status_id']);
});

it('validates status belongs to same project as task', function () {
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $status1 = TaskStatus::factory()->create(['project_id' => $project1->id]);
    $status2 = TaskStatus::factory()->create(['project_id' => $project2->id]);

    $task = Task::factory()->create([
        'project_id' => $project1->id,
        'status_id'  => $status1->id,
    ]);

    $this->actingAsUser();

    // Try to update task with status from different project
    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status_id']);

    // Verify task status was not updated
    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'status_id' => $status1->id,
    ]);
});

it('prevents unauthorized users from updating task status', function () {
    $project = Project::factory()->create();
    $status1 = TaskStatus::factory()->create(['project_id' => $project->id]);
    $status2 = TaskStatus::factory()->create(['project_id' => $project->id]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    // Not authenticated - JSON requests return 401 instead of redirecting
    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertStatus(401);

    // Verify task status was not updated
    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'status_id' => $status1->id,
    ]);
});

it('updating one task status does not change another task', function () {
    $project = Project::factory()->create();
    $status1 = TaskStatus::factory()->create(['project_id' => $project->id]);
    $status2 = TaskStatus::factory()->create(['project_id' => $project->id]);
    $status3 = TaskStatus::factory()->create(['project_id' => $project->id]);

    $taskA = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    $taskB = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status2->id,
    ]);

    $this->actingAsUser();

    $response = $this->patchJson(route('task.updateStatus', $taskA), [
        'status_id' => $status3->id,
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('tasks', [
        'id'        => $taskA->id,
        'status_id' => $status3->id,
    ]);

    $this->assertDatabaseHas('tasks', [
        'id'        => $taskB->id,
        'status_id' => $status2->id,
    ]);
});

it('can update status to null if status_id is nullable', function () {
    $project = Project::factory()->create();
    $status = TaskStatus::factory()->create(['project_id' => $project->id]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
    ]);

    $this->actingAsUser();

    // Try to set status_id to null (should fail due to required validation)
    $response = $this->patchJson(route('task.updateStatus', $task), [
        'status_id' => null,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status_id']);
});
