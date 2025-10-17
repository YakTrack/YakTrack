<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;

it('can view the acceptance criteria index page', function () {
    $this->actingAsUser();

    $response = $this->get(route('acceptance-criteria.index'));

    $response->assertSuccessful();
});

it('displays existing acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'name'        => 'User can login',
        'code'        => 'AC-001',
        'description' => 'User should be able to login with valid credentials.',
    ]);

    $response = $this->get(route('acceptance-criteria.index'));

    $response->assertSuccessful();
});

it('can filter acceptance criteria by project', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create(['name' => 'Project One']);
    $project2 = Project::factory()->create(['name' => 'Project Two']);

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project1->id,
        'name'       => 'Criteria One',
    ]);

    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project2->id,
        'name'       => 'Criteria Two',
    ]);

    $response = $this->get(route('acceptance-criteria.index', ['project_id' => $project1->id]));

    $response->assertSuccessful();
});

it('can navigate to create page from index', function () {
    $this->actingAsUser();

    $response = $this->get(route('acceptance-criteria.create'));

    $response->assertSuccessful();
});

it('can navigate to show page from criteria link', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('requires authentication to view index', function () {
    $response = $this->get(route('acceptance-criteria.index'));

    $response->assertRedirect(route('login'));
});

it('paginates results when there are many criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    // Create more than 15 criteria (default pagination limit)
    AcceptanceCriteria::factory()->count(20)->create([
        'project_id' => $project->id,
    ]);

    $response = $this->get(route('acceptance-criteria.index'));

    $response->assertSuccessful();
});
