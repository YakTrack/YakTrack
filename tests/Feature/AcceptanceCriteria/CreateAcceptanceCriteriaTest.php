<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;

it('can view the page to create acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('acceptance-criteria.create'));

    $response->assertSuccessful();
    $response->assertSee($project->name);
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
