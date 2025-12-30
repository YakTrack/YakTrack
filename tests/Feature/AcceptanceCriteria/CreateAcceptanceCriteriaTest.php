<?php

use App\Models\AcceptanceCriteria;
use App\Models\Feature;
use App\Models\Project;

it('can view the page to create acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('acceptance-criteria.create'));

    $response->assertSuccessful();
    $response->assertSee($project->name);
});

it('prefills project and feature when query parameters are provided', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->forProject($project)->create();

    $response = $this->get(route('acceptance-criteria.create', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
            ->component('AcceptanceCriteria/Edit')
            ->has('prefill')
            ->where('prefill.project_id', (string) $project->id)
            ->where('prefill.feature_id', (string) $feature->id)
    );
});

it('can submit a post request to create acceptance criteria', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id'  => $project->id,
        'code'        => 'AC-001',
        'name'        => 'User can login',
        'description' => 'User should be able to login with valid credentials.',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id'  => $project->id,
        'code'        => 'AC-001',
        'name'        => 'User can login',
        'description' => 'User should be able to login with valid credentials.',
        'is_active'   => true,
    ]);

    // Check that a version was created
    $criteria = AcceptanceCriteria::where('name', 'User can login')->first();
    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $criteria->id,
        'code'                   => 'AC-001',
        'name'                   => 'User can login',
        'description'            => 'User should be able to login with valid credentials.',
        'version_number'         => 1,
    ]);
});

it('can create acceptance criteria without code and description', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'name'       => 'User can logout',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id'  => $project->id,
        'code'        => null,
        'name'        => 'User can logout',
        'description' => null,
        'is_active'   => true,
    ]);
});

it('cannot create acceptance criteria with duplicate code for same project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
    ]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Another criteria',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('code');

    $this->assertDatabaseMissing('acceptance_criteria', [
        'name' => 'Another criteria',
    ]);
});

it('can create acceptance criteria with same code for different projects', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    AcceptanceCriteria::factory()->create([
        'project_id' => $project1->id,
        'code'       => 'AC-001',
    ]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project2->id,
        'code'       => 'AC-001',
        'name'       => 'Another criteria',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project2->id,
        'code'       => 'AC-001',
        'name'       => 'Another criteria',
    ]);
});

it('automatically prepends feature code to acceptance criteria code when feature has code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->withCode('FEAT-001')->forProject($project)->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-001',
        'name'       => 'User can login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-001',
        'name'       => 'User can login',
    ]);
});

it('does not prepend feature code when feature has no code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->forProject($project)->create(['code' => null]);

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-001',
        'name'       => 'User can login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-001',
        'name'       => 'User can login',
    ]);
});

it('does not duplicate feature code prefix if code already starts with feature code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->withCode('FEAT-001')->forProject($project)->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-001',
        'name'       => 'User can login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-001',
        'name'       => 'User can login',
    ]);
});

it('does not prepend feature code when no feature is selected', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'User can login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => null,
        'code'       => 'AC-001',
        'name'       => 'User can login',
    ]);
});

it('does not prepend feature code when acceptance criteria code is empty', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->withCode('FEAT-001')->forProject($project)->create();

    $response = $this->post(route('acceptance-criteria.store'), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'name'       => 'User can login',
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => null,
        'name'       => 'User can login',
    ]);
});
