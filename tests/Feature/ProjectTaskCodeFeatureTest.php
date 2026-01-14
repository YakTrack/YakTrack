<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\User;

it('can create a project with a task code prefix', function () {
    $client = Client::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('project.store'), [
        'name'             => 'Test Project',
        'description'      => 'Test Description',
        'task_code_prefix' => 'TEST',
        'client_id'        => $client->id,
    ]);

    $response->assertRedirect(route('project.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'name'             => 'Test Project',
        'task_code_prefix' => 'TEST',
    ]);
});

it('can create a project without a task code prefix', function () {
    $client = Client::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('project.store'), [
        'name'        => 'Test Project',
        'description' => 'Test Description',
        'client_id'   => $client->id,
    ]);

    $response->assertRedirect(route('project.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('projects', [
        'name'             => 'Test Project',
        'task_code_prefix' => null,
    ]);
});

it('can update a project to add a task code prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => null,
    ]);

    $response = $this->actingAs($user)->patch(route('project.update', $project), [
        'name'             => $project->name,
        'description'      => $project->description,
        'task_code_prefix' => 'ABCD',
        'client_id'        => $project->client_id,
    ]);

    $response->assertRedirect(route('project.index'));

    $this->assertDatabaseHas('projects', [
        'id'               => $project->id,
        'task_code_prefix' => 'ABCD',
    ]);
});

it('can update a project to remove a task code prefix', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'task_code_prefix' => 'ABCD',
    ]);

    $response = $this->actingAs($user)->patch(route('project.update', $project), [
        'name'             => $project->name,
        'description'      => $project->description,
        'task_code_prefix' => null,
        'client_id'        => $project->client_id,
    ]);

    $response->assertRedirect(route('project.index'));

    $this->assertDatabaseHas('projects', [
        'id'               => $project->id,
        'task_code_prefix' => null,
    ]);
});

it('validates task code prefix maximum length', function () {
    $client = Client::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('project.store'), [
        'name'             => 'Test Project',
        'description'      => 'Test Description',
        'task_code_prefix' => str_repeat('A', 21), // 21 characters, exceeds max of 20
        'client_id'        => $client->id,
    ]);

    $response->assertSessionHasErrors('task_code_prefix');
});
