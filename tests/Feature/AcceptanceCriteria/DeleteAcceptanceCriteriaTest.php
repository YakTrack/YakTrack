<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\Task;
use App\Models\TestResult;
use App\Models\TestRun;

it('can soft delete acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that is_active is set to false
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria->id,
        'is_active' => false,
    ]);
});

it('can delete criteria with linked tasks', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Task',
    ]);

    $criteria->tasks()->attach($task->id);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that the criteria is still deleted (soft delete)
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria->id,
        'is_active' => false,
    ]);
});

it('can delete criteria with test results', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Run',
    ]);

    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
    ]);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that the criteria is still deleted (soft delete)
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria->id,
        'is_active' => false,
    ]);
});

it('can delete criteria without any dependencies', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that is_active is set to false
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria->id,
        'is_active' => false,
    ]);
});

it('preserves version history when deleting criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    // Create a version
    $criteria->createVersion([
        'code'        => 'AC-001',
        'name'        => 'Test Criteria',
        'description' => 'Test description',
    ], auth()->id());

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that versions are preserved
    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $criteria->id,
        'version_number'         => 1,
    ]);
});

it('requires authentication to delete criteria', function () {
    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent criteria deletion', function () {
    $this->actingAsUser();

    $response = $this->delete('/acceptance-criteria/999');

    $response->assertNotFound();
});

it('shows success message after deletion', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->delete(route('acceptance-criteria.destroy', $criteria));

    $response->assertRedirect(route('acceptance-criteria.index'));
    $response->assertSessionHas('success');
});

it('can delete multiple criteria at once', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria One',
    ]);
    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria Two',
    ]);

    $response1 = $this->delete(route('acceptance-criteria.destroy', $criteria1));
    $response2 = $this->delete(route('acceptance-criteria.destroy', $criteria2));

    $response1->assertRedirect(route('acceptance-criteria.index'));
    $response2->assertRedirect(route('acceptance-criteria.index'));

    // Check that both criteria are soft deleted
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria1->id,
        'is_active' => false,
    ]);
    $this->assertDatabaseHas('acceptance_criteria', [
        'id'        => $criteria2->id,
        'is_active' => false,
    ]);
});
