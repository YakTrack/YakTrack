<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\TestRun;
use App\TestResultStatus;

it('can view the page to create test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('test-run.create'));

    $response->assertSuccessful();
});

it('can submit a post request to create test run', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria1 = AcceptanceCriteria::factory()->create(['project_id' => $project->id]);
    $criteria2 = AcceptanceCriteria::factory()->create(['project_id' => $project->id]);

    $response = $this->post(route('test-run.store'), [
        'project_id'              => $project->id,
        'name'                    => 'Sprint 1 Test Run',
        'description'             => 'Testing sprint 1 features',
        'executed_at'             => now()->format('Y-m-d\TH:i'),
        'acceptance_criteria_ids' => [$criteria1->id, $criteria2->id],
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_runs', [
        'project_id'          => $project->id,
        'name'                => 'Sprint 1 Test Run',
        'description'         => 'Testing sprint 1 features',
        'executed_by_user_id' => auth()->id(),
    ]);

    // Check that test results were created
    $testRun = TestRun::where('name', 'Sprint 1 Test Run')->first();
    $this->assertDatabaseHas('test_results', [
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria1->id,
        'status'                 => TestResultStatus::Pending,
    ]);
    $this->assertDatabaseHas('test_results', [
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria2->id,
        'status'                 => TestResultStatus::Pending,
    ]);
});

it('can create test run without description', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();
    $criteria = AcceptanceCriteria::factory()->create(['project_id' => $project->id]);

    $response = $this->post(route('test-run.store'), [
        'project_id'              => $project->id,
        'name'                    => 'Quick Test Run',
        'executed_at'             => now()->format('Y-m-d\TH:i'),
        'acceptance_criteria_ids' => [$criteria->id],
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_runs', [
        'project_id'  => $project->id,
        'name'        => 'Quick Test Run',
        'description' => null,
    ]);
});

it('cannot create test run without acceptance criteria', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('test-run.store'), [
        'project_id'              => $project->id,
        'name'                    => 'Empty Test Run',
        'executed_at'             => now()->format('Y-m-d\TH:i'),
        'acceptance_criteria_ids' => [],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('acceptance_criteria_ids');

    $this->assertDatabaseMissing('test_runs', [
        'name' => 'Empty Test Run',
    ]);
});
