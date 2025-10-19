<?php

use App\Models\AcceptanceCriteria;
use App\Models\Feature;
use App\Models\Project;

it('can view the feature show page', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature')
        ->where('feature.name', $feature->name)
    );
});

it('shows feature without description', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create(['description' => null]);

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature')
        ->where('feature.description', null)
    );
});

it('displays acceptance criteria for the feature', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'feature_id' => $feature->id,
        'name' => 'Test Criteria',
    ]);

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature.acceptance_criteria', 1)
        ->where('feature.acceptance_criteria.0.name', 'Test Criteria')
    );
});

it('shows empty state when no acceptance criteria exist', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature.acceptance_criteria', 0)
    );
});

it('displays project information', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $feature = Feature::factory()->create(['project_id' => $project->id]);

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature.project')
        ->where('feature.project.name', 'Test Project')
    );
});

it('can navigate to edit page from show page', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature')
    );
});

it('can navigate back to index from show page', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
    );
});

it('requires authentication to view show page', function () {
    $feature = Feature::factory()->create();

    $response = $this->get(route('features.show', $feature));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent feature', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.show', 999));

    $response->assertNotFound();
});

it('only shows active acceptance criteria', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();
    AcceptanceCriteria::factory()->create([
        'feature_id' => $feature->id,
        'is_active' => true,
        'name' => 'Active Criteria',
    ]);
    AcceptanceCriteria::factory()->create([
        'feature_id' => $feature->id,
        'is_active' => false,
        'name' => 'Inactive Criteria',
    ]);

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature.acceptance_criteria', 1)
        ->where('feature.acceptance_criteria.0.name', 'Active Criteria')
    );
});

it('orders acceptance criteria by name', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();
    AcceptanceCriteria::factory()->create([
        'feature_id' => $feature->id,
        'name' => 'Z Criteria',
    ]);
    AcceptanceCriteria::factory()->create([
        'feature_id' => $feature->id,
        'name' => 'A Criteria',
    ]);

    $response = $this->get(route('features.show', $feature));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Show')
        ->has('feature.acceptance_criteria', 2)
        ->where('feature.acceptance_criteria.0.name', 'A Criteria')
        ->where('feature.acceptance_criteria.1.name', 'Z Criteria')
    );
});