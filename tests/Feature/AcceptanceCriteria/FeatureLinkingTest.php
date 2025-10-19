<?php

use App\Models\AcceptanceCriteria;
use App\Models\Feature;
use App\Models\Project;

it('can create acceptance criteria with a feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project->id,
        'name' => 'User Authentication',
    ]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'name' => 'User can login with valid credentials',
        'description' => 'Given valid credentials, user should be able to login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'name' => 'User can login with valid credentials',
        'description' => 'Given valid credentials, user should be able to login',
        'is_active' => true,
    ]);
});

it('can create acceptance criteria without a feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => null,
        'name' => 'User can login with valid credentials',
        'description' => 'Given valid credentials, user should be able to login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => null,
        'name' => 'User can login with valid credentials',
        'description' => 'Given valid credentials, user should be able to login',
        'is_active' => true,
    ]);
});

it('can update acceptance criteria to link to a feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project->id,
        'name' => 'User Authentication',
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'feature_id' => null,
    ]);

    $response = $this->patch(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'name' => $criteria->name,
        'description' => $criteria->description,
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $criteria->refresh();
    expect($criteria->feature_id)->toBe($feature->id);
    expect($criteria->feature->name)->toBe('User Authentication');
});

it('can update acceptance criteria to remove feature link', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'feature_id' => $feature->id,
    ]);

    $response = $this->patch(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'feature_id' => null,
        'name' => $criteria->name,
        'description' => $criteria->description,
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $criteria->refresh();
    expect($criteria->feature_id)->toBeNull();
});

it('validates that feature belongs to the same project', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project2->id,
    ]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project1->id,
        'feature_id' => $feature->id,
        'name' => 'User can login with valid credentials',
    ]);

    $response->assertSessionHasErrors(['feature_id']);
});

it('validates that feature exists', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => 999,
        'name' => 'User can login with valid credentials',
    ]);

    $response->assertSessionHasErrors(['feature_id']);
});

it('validates that feature is active', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project->id,
        'is_active' => false,
    ]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'name' => 'User can login with valid credentials',
    ]);

    $response->assertSessionHasErrors(['feature_id']);
});

it('shows feature information in acceptance criteria show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->create([
        'project_id' => $project->id,
        'name' => 'User Authentication',
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'feature_id' => $feature->id,
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('AcceptanceCriteria/Show')
        ->has('criteria.feature')
        ->where('criteria.feature.name', 'User Authentication')
    );
});

it('shows no feature when acceptance criteria has no feature', function () {
    $this->actingAsUser();

    $criteria = AcceptanceCriteria::factory()->create([
        'feature_id' => null,
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('AcceptanceCriteria/Show')
        ->has('criteria.feature')
        ->where('criteria.feature', null)
    );
});
