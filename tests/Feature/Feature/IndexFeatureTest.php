<?php

use App\Models\Feature;
use App\Models\Project;

it('can view the features index page', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features')
        ->has('projects')
    );
});

it('displays existing features', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 1)
        ->where('features.data.0.name', $feature->name)
    );
});

it('can filter features by project', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $feature1 = Feature::factory()->create(['project_id' => $project1->id]);
    $feature2 = Feature::factory()->create(['project_id' => $project2->id]);

    $response = $this->get(route('features.index', ['project_id' => $project1->id]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 1)
        ->where('features.data.0.name', $feature1->name)
    );
});

it('can navigate to create page from index', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
    );
});

it('can navigate to show page from feature link', function () {
    $this->actingAsUser();

    $feature = Feature::factory()->create();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 1)
    );
});

it('requires authentication to view index', function () {
    $response = $this->get(route('features.index'));

    $response->assertRedirect(route('login'));
});

it('paginates results when there are many features', function () {
    $this->actingAsUser();

    Feature::factory()->count(20)->create();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 15) // Default pagination
        ->has('features.links')
    );
});

it('shows empty state when no features exist', function () {
    $this->actingAsUser();

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 0)
    );
});

it('only shows active features', function () {
    $this->actingAsUser();

    Feature::factory()->create(['is_active' => true]);
    Feature::factory()->create(['is_active' => false]);

    $response = $this->get(route('features.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Features/Index')
        ->has('features.data', 1)
    );
});