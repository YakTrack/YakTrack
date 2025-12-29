<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\User;
use App\TestResultStatus;

it('can view the test run show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Sprint 1 Testing',
        'description'         => 'Testing all sprint 1 features',
        'executed_at'         => now(),
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('shows test run without description', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Quick Test',
        'description'         => null,
        'executed_at'         => now(),
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('displays test results for the test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'User can login',
    ]);
    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'User can logout',
    ]);

    $testResult1 = TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria1->id,
        'status'                 => TestResultStatus::Passed,
        'notes'                  => 'Login works correctly',
    ]);

    $testResult2 = TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria2->id,
        'status'                 => TestResultStatus::Failed,
        'notes'                  => 'Logout button not working',
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('shows empty state when no test results exist', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('displays execution information', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $user = User::factory()->create(['name' => 'Test User']);
    $executionDate = now()->subDays(1);

    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_at'         => $executionDate,
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('can navigate back to index from show page', function () {
    $this->actingAsUser();

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('requires authentication to view show page', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent test run', function () {
    $this->actingAsUser();

    $response = $this->get('/test-run/999');

    $response->assertNotFound();
});

it('displays test run summary statistics', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 1',
    ]);
    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 2',
    ]);
    $criteria3 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 3',
    ]);

    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria1->id,
        'status'                 => TestResultStatus::Passed,
    ]);

    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria2->id,
        'status'                 => TestResultStatus::Failed,
    ]);

    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria3->id,
        'status'                 => TestResultStatus::Skipped,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('shows evidence for test results', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status'                 => TestResultStatus::Passed,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
});

it('can add acceptance criteria to existing test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 1',
    ]);

    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 2',
    ]);

    // Add first criteria to test run
    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria1->id,
        'status'                 => TestResultStatus::Passed,
    ]);

    // Add second criteria via the new endpoint
    $response = $this->post(route('test-run.test-result.store', $testRun), [
        'acceptance_criteria_id' => $criteria2->id,
        'status'                 => 'skipped',
        'notes'                  => 'Initial notes',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('test_results', [
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria2->id,
        'status'                 => TestResultStatus::Skipped,
        'notes'                  => 'Initial notes',
    ]);
});

it('prevents adding duplicate acceptance criteria to test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 1',
    ]);

    // Add criteria to test run
    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status'                 => TestResultStatus::Passed,
    ]);

    // Try to add the same criteria again
    $response = $this->post(route('test-run.test-result.store', $testRun), [
        'acceptance_criteria_id' => $criteria->id,
        'status'                 => 'skipped',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Verify only one test result exists for this criteria
    $this->assertEquals(1, TestResult::where('test_run_id', $testRun->id)
        ->where('acceptance_criteria_id', $criteria->id)
        ->count());
});

it('prevents adding acceptance criteria from different project', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project1->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project2->id,
        'name'       => 'Criteria from different project',
    ]);

    // Try to add criteria from different project
    $response = $this->post(route('test-run.test-result.store', $testRun), [
        'acceptance_criteria_id' => $criteria->id,
        'status'                 => 'skipped',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Verify no test result was created
    $this->assertDatabaseMissing('test_results', [
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
    ]);
});

it('includes available acceptance criteria in show page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $criteria1 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 1',
    ]);

    $criteria2 = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name'       => 'Criteria 2',
    ]);

    // Add only first criteria to test run
    TestResult::factory()->create([
        'test_run_id'            => $testRun->id,
        'acceptance_criteria_id' => $criteria1->id,
        'status'                 => TestResultStatus::Passed,
    ]);

    $response = $this->get(route('test-run.show', $testRun));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('availableCriteria')
        ->where('availableCriteria.0.id', $criteria2->id)
    );
});
