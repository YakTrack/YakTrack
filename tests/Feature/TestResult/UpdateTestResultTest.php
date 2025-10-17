<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\User;
use App\TestResultStatus;

it('can update test result status', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
        'notes' => 'Test passed successfully',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Passed->value,
        'notes' => 'Test passed successfully',
    ]);
});

it('can update test result with all status types', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    // Test Passed status
    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
        'notes' => 'Test passed',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Passed->value,
    ]);

    // Test Failed status
    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Failed->value,
        'notes' => 'Test failed',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Failed->value,
    ]);

    // Test Skipped status
    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Skipped->value,
        'notes' => 'Test skipped',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Skipped->value,
    ]);
});

it('can update test result without notes', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
        'notes' => 'Original notes',
    ]);

    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
        'notes' => null,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Passed->value,
        'notes' => null,
    ]);
});

it('shows validation errors for invalid status', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => 'invalid_status',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('status');
});

it('requires authentication to update test result', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
    ]);

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent test result', function () {
    $this->actingAsUser();

    $response = $this->patch(route('test-result.update', 999), [
        'status' => TestResultStatus::Passed->value,
    ]);

    $response->assertNotFound();
});

it('shows success message after update', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
        'notes' => 'Test passed',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('can update test result multiple times', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $criteria = AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Criteria',
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
        'acceptance_criteria_id' => $criteria->id,
        'status' => TestResultStatus::Pending,
    ]);

    // First update
    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Passed->value,
        'notes' => 'First update',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Passed->value,
        'notes' => 'First update',
    ]);

    // Second update
    $response = $this->patch(route('test-result.update', $testResult), [
        'status' => TestResultStatus::Failed->value,
        'notes' => 'Second update',
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('test_results', [
        'id' => $testResult->id,
        'status' => TestResultStatus::Failed->value,
        'notes' => 'Second update',
    ]);
});