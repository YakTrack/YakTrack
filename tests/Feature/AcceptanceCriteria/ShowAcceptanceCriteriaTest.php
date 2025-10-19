<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\Task;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\User;
use App\TestResultStatus;

it('can view the acceptance criteria show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'name'        => 'Test Criteria',
        'code'        => 'AC-001',
        'description' => 'Test description',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('shows criteria without code', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->withoutCode()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('shows criteria without description', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $criteria = AcceptanceCriteria::factory()->withoutDescription()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('displays version history', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    // Create multiple versions
    $criteria->createVersion([
        'code'        => 'AC-001',
        'name'        => 'Test Criteria',
        'description' => 'Original description',
    ], $user->id);

    $criteria->updateWithVersion([
        'name'        => 'Updated Criteria',
        'description' => 'Updated description',
    ], $user->id);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('displays linked tasks', function () {
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

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('displays test results history', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status'                 => TestResultStatus::Passed,
        'notes'                  => 'Test passed successfully',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('can navigate to edit page from show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.edit', $criteria));

    $response->assertSuccessful();
});

it('can navigate back to index from show page', function () {
    $this->actingAsUser();

    $response = $this->get(route('acceptance-criteria.index'));

    $response->assertSuccessful();
});

it('requires authentication to view show page', function () {
    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent criteria', function () {
    $this->actingAsUser();

    $response = $this->get('/acceptance-criteria/999');

    $response->assertNotFound();
});

it('shows empty state for criteria with no linked tasks', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});

it('shows empty state for criteria with no test results', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $response = $this->get(route('acceptance-criteria.show', $criteria));

    $response->assertSuccessful();
});
