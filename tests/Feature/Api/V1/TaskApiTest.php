<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/tasks')
        ->assertUnauthorized();
});

it('can list tasks', function () {
    Task::factory()->count(3)->create();

    $this->getJson('/api/v1/tasks')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'name', 'description', 'project_id', 'created_at', 'updated_at']],
        ]);
});

it('can create a task', function () {
    $project = Project::factory()->create();

    $this->postJson('/api/v1/tasks', [
        'name'        => 'New API Task',
        'description' => 'Created via API',
        'project_id'  => $project->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'New API Task');

    $this->assertDatabaseHas('tasks', ['name' => 'New API Task']);
});

it('assigns default status when creating a task with a project', function () {
    $project = Project::factory()->create();
    $status = TaskStatus::factory()->create([
        'project_id' => $project->id,
        'is_default' => true,
        'name'       => 'To Do',
    ]);

    $response = $this->postJson('/api/v1/tasks', [
        'name'       => 'Task With Status',
        'project_id' => $project->id,
    ])->assertCreated();

    expect($response->json('data.status_id'))->toBe($status->id);
});

it('validates required fields when creating a task', function () {
    $this->postJson('/api/v1/tasks', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates unique task name per project', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['name' => 'Duplicate', 'project_id' => $project->id]);

    $this->postJson('/api/v1/tasks', [
        'name'       => 'Duplicate',
        'project_id' => $project->id,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('can show a task', function () {
    $task = Task::factory()->create(['name' => 'Show Task']);

    $this->getJson("/api/v1/tasks/{$task->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Show Task');
});

it('can update a task', function () {
    $task = Task::factory()->create(['name' => 'Old Task']);

    $this->putJson("/api/v1/tasks/{$task->id}", ['name' => 'Updated Task'])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Task');
});

it('can delete a task', function () {
    $task = Task::factory()->create();

    $this->deleteJson("/api/v1/tasks/{$task->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
