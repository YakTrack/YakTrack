<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;

it('displays task status on show page', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $status = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
        'color'      => '#3b82f6',
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
        'name'       => 'Test Task',
    ]);

    $response = $this->actingAs($user)->get(route('task.show', $task));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Task/Show')
        ->has('task.task_status')
        ->where('task.task_status.id', $status->id)
        ->where('task.task_status.name', 'In Progress')
    );
});

it('loads available task statuses on show page', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $status1 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'To Do',
    ]);

    $status2 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    $response = $this->actingAs($user)->get(route('task.show', $task));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Task/Show')
        ->has('task.project.task_statuses', 2)
    );
});

it('can change task status via updateStatus endpoint', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $status1 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'To Do',
    ]);

    $status2 = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'name'       => 'In Progress',
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status1->id,
    ]);

    $response = $this->actingAs($user)->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'status_id' => $status2->id,
    ]);
});

it('validates status belongs to same project when changing status', function () {
    $user = User::factory()->create();
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $status1 = TaskStatus::factory()->create([
        'project_id' => $project1->id,
    ]);

    $status2 = TaskStatus::factory()->create([
        'project_id' => $project2->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project1->id,
        'status_id'  => $status1->id,
    ]);

    $response = $this->actingAs($user)->patchJson(route('task.updateStatus', $task), [
        'status_id' => $status2->id,
    ]);

    $response->assertStatus(422);

    $this->assertDatabaseHas('tasks', [
        'id'        => $task->id,
        'status_id' => $status1->id,
    ]);
});

it('requires valid status_id when changing status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $status = TaskStatus::factory()->create([
        'project_id' => $project->id,
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'status_id'  => $status->id,
    ]);

    $response = $this->actingAs($user)->patchJson(route('task.updateStatus', $task), [
        'status_id' => 99999,
    ]);

    $response->assertStatus(422);
});
