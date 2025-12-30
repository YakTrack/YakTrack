<?php

use App\Models\AcceptanceCriteria;
use App\Models\AcceptanceCriteriaVersion;
use App\Models\Feature;
use App\Models\Project;

it('can view the edit page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'name'        => 'Test Criteria',
        'code'        => 'AC-001',
        'description' => 'Test description',
    ]);

    $response = $this->get(route('acceptance-criteria.edit', $criteria));

    $response->assertSuccessful();
});

it('can update acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'name'        => 'Original Name',
        'code'        => 'AC-001',
        'description' => 'Original description',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id'  => $project->id,
        'name'        => 'Updated Name',
        'code'        => 'AC-002',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'          => $criteria->id,
        'name'        => 'Updated Name',
        'code'        => 'AC-002',
        'description' => 'Updated description',
    ]);

    // Check that a new version was created
    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $criteria->id,
        'name'                   => 'Updated Name',
        'code'                   => 'AC-002',
        'description'            => 'Updated description',
        'version_number'         => 1,
    ]);
});

it('can update acceptance criteria without code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Original Name',
        'code'       => 'AC-001',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id'  => $project->id,
        'name'        => 'Updated Name',
        'code'        => null,
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'          => $criteria->id,
        'name'        => 'Updated Name',
        'code'        => null,
        'description' => 'Updated description',
    ]);
});

it('can update acceptance criteria without description', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'name'        => 'Original Name',
        'description' => 'Original description',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id'  => $project->id,
        'name'        => 'Updated Name',
        'description' => null,
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'          => $criteria->id,
        'name'        => 'Updated Name',
        'description' => null,
    ]);
});

it('shows validation errors for missing required fields', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'name'       => '', // Empty name
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('name');
});

it('cannot update with duplicate code for same project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
    ]);
    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-002',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria2), [
        'project_id' => $project->id,
        'name'       => 'Updated Name',
        'code'       => 'AC-001', // Duplicate code
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('code');
});

it('can update with same code for different projects', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project1->id,
        'code'       => 'AC-001',
    ]);
    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project2->id,
        'code'       => 'AC-002',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria2), [
        'project_id' => $project2->id,
        'name'       => 'Updated Name',
        'code'       => 'AC-001', // Same code as criteria1 but different project
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria2));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'   => $criteria2->id,
        'code' => 'AC-001',
    ]);
});

it('creates version with correct user information', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Original Name',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id'  => $project->id,
        'name'        => 'Updated Name',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $version = AcceptanceCriteriaVersion::where('acceptance_criteria_id', $criteria->id)
        ->where('version_number', 1)
        ->first();

    expect($version)->not->toBeNull();
    expect($version->changed_by_user_id)->toBe(auth()->id());
});

it('requires authentication to edit criteria', function () {
    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.edit', $criteria));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent criteria edit page', function () {
    $this->actingAsUser();

    $response = $this->get('/acceptance-criteria/999/edit');

    $response->assertNotFound();
});

it('can cancel editing and return to show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('automatically prepends feature code when updating acceptance criteria with feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->withCode('FEAT-001')->forProject($project)->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Original Name',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-002',
        'name'       => 'Updated Name',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'         => $criteria->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-002',
        'name'       => 'Updated Name',
    ]);
});

it('does not duplicate feature code prefix when updating if code already starts with feature code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->withCode('FEAT-001')->forProject($project)->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-001',
        'name'       => 'Original Name',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-002',
        'name'       => 'Updated Name',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'         => $criteria->id,
        'feature_id' => $feature->id,
        'code'       => 'FEAT-001:AC-002',
        'name'       => 'Updated Name',
    ]);
});

it('does not prepend feature code when updating if feature has no code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $feature = Feature::factory()->forProject($project)->create(['code' => null]);
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Original Name',
    ]);

    $response = $this->put(route('acceptance-criteria.update', $criteria), [
        'project_id' => $project->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-002',
        'name'       => 'Updated Name',
    ]);

    $response->assertRedirect(route('acceptance-criteria.show', $criteria));

    $this->assertDatabaseHas('acceptance_criteria', [
        'id'         => $criteria->id,
        'feature_id' => $feature->id,
        'code'       => 'AC-002',
        'name'       => 'Updated Name',
    ]);
});
