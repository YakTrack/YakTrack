<?php

use App\Models\Project;
use App\Models\TestResult;
use App\Models\TestRun;
use App\Models\User;

it('can delete test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('test-run.index'));

    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun->id,
    ]);
});

it('deletes associated test results when deleting test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('test-run.index'));

    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun->id,
    ]);

    $this->assertDatabaseMissing('test_results', [
        'id' => $testResult->id,
    ]);
});

it('deletes associated evidence when deleting test run', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $testResult = TestResult::factory()->create([
        'test_run_id' => $testRun->id,
    ]);

    $evidence = $testResult->evidence()->create([
        'type' => 'text',
        'content' => 'Test evidence',
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('test-run.index'));

    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun->id,
    ]);

    $this->assertDatabaseMissing('test_result_evidence', [
        'id' => $evidence->id,
    ]);
});

it('requires authentication to delete test run', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent test run deletion', function () {
    $this->actingAsUser();

    $response = $this->delete(route('test-run.destroy', 999));

    $response->assertNotFound();
});

it('shows success message after deletion', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('test-run.index'));
    $response->assertSessionHas('success');
});

it('can delete multiple test runs', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    
    $testRun1 = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run 1',
        'executed_by_user_id' => $user->id,
    ]);
    
    $testRun2 = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run 2',
        'executed_by_user_id' => $user->id,
    ]);

    $response1 = $this->delete(route('test-run.destroy', $testRun1));
    $response2 = $this->delete(route('test-run.destroy', $testRun2));

    $response1->assertRedirect(route('test-run.index'));
    $response2->assertRedirect(route('test-run.index'));

    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun1->id,
    ]);
    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun2->id,
    ]);
});

it('handles deletion of test run with many test results', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id' => $project->id,
        'name' => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    // Create multiple test results
    TestResult::factory()->count(10)->create([
        'test_run_id' => $testRun->id,
    ]);

    $response = $this->delete(route('test-run.destroy', $testRun));

    $response->assertRedirect(route('test-run.index'));

    $this->assertDatabaseMissing('test_runs', [
        'id' => $testRun->id,
    ]);

    $this->assertDatabaseMissing('test_results', [
        'test_run_id' => $testRun->id,
    ]);
});