<?php

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/sprints')
        ->assertUnauthorized();
});

it('can list sprints', function () {
    Sprint::factory()->count(3)->create();

    $this->getJson('/api/v1/sprints')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'name', 'is_open', 'projects', 'created_at', 'updated_at']],
        ]);
});

it('can create a sprint', function () {
    $project = Project::factory()->create();

    $this->postJson('/api/v1/sprints', [
        'name'        => 'Sprint 1',
        'is_open'     => true,
        'project_ids' => [$project->id],
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Sprint 1')
        ->assertJsonPath('data.is_open', true);

    $this->assertDatabaseHas('sprints', ['name' => 'Sprint 1']);
    $this->assertDatabaseHas('project_sprint', ['project_id' => $project->id]);
});

it('validates required fields when creating a sprint', function () {
    $this->postJson('/api/v1/sprints', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'project_ids']);
});

it('validates unique sprint name', function () {
    $sprint = Sprint::factory()->create();
    $existingName = $sprint->name;

    $project = Project::factory()->create();

    $this->postJson('/api/v1/sprints', [
        'name'        => $existingName,
        'project_ids' => [$project->id],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates project_ids must contain valid projects', function () {
    $this->postJson('/api/v1/sprints', [
        'name'        => 'Bad Sprint',
        'project_ids' => [99999],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['project_ids.0']);
});

it('can show a sprint', function () {
    $sprint = Sprint::factory()->create();

    $this->getJson("/api/v1/sprints/{$sprint->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.id', $sprint->id)
        ->assertJsonStructure(['data' => ['id', 'name', 'is_open', 'projects']]);
});

it('can update a sprint', function () {
    $sprint = Sprint::factory()->create();
    $project = Project::factory()->create();

    $this->putJson("/api/v1/sprints/{$sprint->id}", [
        'name'        => 'Updated Sprint',
        'is_open'     => false,
        'project_ids' => [$project->id],
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Sprint')
        ->assertJsonPath('data.is_open', false);
});

it('can delete a sprint', function () {
    $sprint = Sprint::factory()->create();

    $this->deleteJson("/api/v1/sprints/{$sprint->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
});
