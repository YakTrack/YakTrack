<?php

use App\Models\Feature;
use App\Models\Project;

it('can view the page to edit a feature', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.edit', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Edit')
        ->has('feature')
        ->has('projects')
    );
});

it('can update a feature', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create([
        'name' => 'Old Feature Name',
        'description' => 'Old description',
    ]);

    $response = $this->put(route('features.update', $feature), [
        'project_id' => $feature->project_id,
        'name' => 'New Feature Name',
        'description' => 'New description',
    ]);

    $response->assertRedirect(route('features.show', $feature));

    $feature->refresh();
    expect($feature->name)->toBe('New Feature Name');
    expect($feature->description)->toBe('New description');
});

it('can update a feature without description', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create([
        'description' => 'Old description',
    ]);

    $response = $this->put(route('features.update', $feature), [
        'project_id' => $feature->project_id,
        'name' => $feature->name,
        'description' => '',
    ]);

    $response->assertRedirect(route('features.show', $feature));

    $feature->refresh();
    expect($feature->description)->toBeNull();
});

it('shows validation errors for missing required fields', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->put(route('features.update', $feature), []);

    $response->assertSessionHasErrors(['project_id', 'name']);
});

it('cannot update with duplicate name for same project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature1 = Feature::factory()->create([
        'project_id' => $project->id,
        'name' => 'User Authentication',
    ]);
    $feature2 = Feature::factory()->create([
        'project_id' => $project->id,
        'name' => 'User Registration',
    ]);

    $response = $this->put(route('features.update', $feature2), [
        'project_id' => $project->id,
        'name' => 'User Authentication',
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('can update with same name for different projects', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project1->id,
        'name' => 'User Authentication',
    ]);

    $response = $this->put(route('features.update', $feature), [
        'project_id' => $project2->id,
        'name' => 'User Authentication',
    ]);

    $response->assertRedirect(route('features.show', $feature));

    $feature->refresh();
    expect($feature->project_id)->toBe($project2->id);
});

it('requires authentication to edit a feature', function () {
    $feature = Feature::factory()->create();

    $response = $this->get(route('features.edit', $feature));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent feature edit page', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.edit', 999));

    $response->assertNotFound();
});

it('can cancel editing and return to show page', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.edit', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Edit')
        ->has('feature')
    );
});