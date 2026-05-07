<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('returns 401 when unauthenticated', function () {
    app('auth')->forgetGuards();

    $this->getJson('/api/v1/projects')
        ->assertUnauthorized();
});

it('can list projects', function () {
    Project::factory()->count(3)->create();

    $this->getJson('/api/v1/projects')
        ->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [['id', 'name', 'description', 'client_id', 'is_billable', 'created_at', 'updated_at']],
        ]);
});

it('includes client relationship when listing projects', function () {
    $client = Client::factory()->create(['name' => 'API Client']);
    Project::factory()->create(['client_id' => $client->id]);

    $this->getJson('/api/v1/projects')
        ->assertSuccessful()
        ->assertJsonPath('data.0.client.name', 'API Client');
});

it('can create a project', function () {
    $client = Client::factory()->create();

    $this->postJson('/api/v1/projects', [
        'name'        => 'New API Project',
        'description' => 'Built via API',
        'client_id'   => $client->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'New API Project');

    $this->assertDatabaseHas('projects', ['name' => 'New API Project']);
});

it('validates required fields when creating a project', function () {
    $this->postJson('/api/v1/projects', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('can show a project', function () {
    $project = Project::factory()->create(['name' => 'Show Project']);

    $this->getJson("/api/v1/projects/{$project->id}")
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Show Project');
});

it('can update a project', function () {
    $project = Project::factory()->create(['name' => 'Old Project']);

    $this->putJson("/api/v1/projects/{$project->id}", ['name' => 'Updated Project'])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Project');
});

it('can delete a project without tasks', function () {
    $project = Project::factory()->create();

    $this->deleteJson("/api/v1/projects/{$project->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

it('cannot delete a project with tasks', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id]);

    $this->deleteJson("/api/v1/projects/{$project->id}")
        ->assertUnprocessable();
});

it('can archive a project', function () {
    $project = Project::factory()->create();

    $this->patchJson("/api/v1/projects/{$project->id}/archive")
        ->assertSuccessful();

    expect($project->fresh()->archived_at)->not->toBeNull();
});

it('can unarchive a project', function () {
    $project = Project::factory()->create();
    $project->archive();

    $this->patchJson("/api/v1/projects/{$project->id}/unarchive")
        ->assertSuccessful();

    expect($project->fresh()->archived_at)->toBeNull();
});
