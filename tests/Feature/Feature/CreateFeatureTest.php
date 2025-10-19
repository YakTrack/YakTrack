<?php

use App\Models\Feature;
use App\Models\Project;

it('can view the page to create a feature', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.create'));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
        ->component('Features/Edit')
        ->has('projects')
    );
});

it('can submit a post request to create a feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('features.store'), [
        'project_id'  => $project->id,
        'name'        => 'User Authentication',
        'description' => 'Features related to user login and authentication',
    ]);

    $response->assertRedirect(route('features.show', Feature::latest()->first()));

    $this->assertDatabaseHas('features', [
        'project_id'  => $project->id,
        'name'        => 'User Authentication',
        'description' => 'Features related to user login and authentication',
        'is_active'   => true,
    ]);
});

it('can create a feature without description', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('features.store'), [
        'project_id' => $project->id,
        'name'       => 'User Authentication',
    ]);

    $response->assertRedirect(route('features.show', Feature::latest()->first()));

    $this->assertDatabaseHas('features', [
        'project_id'  => $project->id,
        'name'        => 'User Authentication',
        'description' => null,
        'is_active'   => true,
    ]);
});

it('cannot create a feature with duplicate name for same project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    Feature::factory()->create([
        'project_id' => $project->id,
        'name'       => 'User Authentication',
    ]);

    $response = $this->post(route('features.store'), [
        'project_id' => $project->id,
        'name'       => 'User Authentication',
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('can create a feature with same name for different projects', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    Feature::factory()->create([
        'project_id' => $project1->id,
        'name'       => 'User Authentication',
    ]);

    $response = $this->post(route('features.store'), [
        'project_id' => $project2->id,
        'name'       => 'User Authentication',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('features', [
        'project_id' => $project2->id,
        'name'       => 'User Authentication',
    ]);
});

it('shows validation errors for missing required fields', function () {
    $this->actingAsUser();

    $response = $this->post(route('features.store'), []);

    $response->assertSessionHasErrors(['project_id', 'name']);
});

it('requires authentication to create a feature', function () {
    $response = $this->get(route('features.create'));

    $response->assertRedirect(route('login'));
});
