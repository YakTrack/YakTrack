<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use App\Models\TestResult;
use App\Models\TestResultEvidence;
use App\Models\TestRun;
use App\Models\User;
use App\EvidenceType;
use App\TestResultStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('can add text evidence to test result', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => 'This is test evidence',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_result_evidence', [
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Text->value,
        'content' => 'This is test evidence',
    ]);
});

it('can add image evidence to test result', function () {
    Storage::fake('public');
    
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
        'status' => TestResultStatus::Passed,
    ]);

    $file = UploadedFile::fake()->image('test-image.jpg');

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Image->value,
        'file' => $file,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_result_evidence', [
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Image->value,
    ]);

    // Check that file was stored
    $evidence = TestResultEvidence::where('test_result_id', $testResult->id)->first();
    expect($evidence->content)->not->toBeNull();
    Storage::disk('public')->assertExists($evidence->content);
});

it('can add multiple pieces of evidence to test result', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    // Add first evidence
    $response1 = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => 'First evidence',
    ]);
    $response1->assertRedirect();

    // Add second evidence
    $response2 = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => 'Second evidence',
    ]);
    $response2->assertRedirect();

    $this->assertDatabaseHas('test_result_evidence', [
        'test_result_id' => $testResult->id,
        'content' => 'First evidence',
    ]);

    $this->assertDatabaseHas('test_result_evidence', [
        'test_result_id' => $testResult->id,
        'content' => 'Second evidence',
    ]);
});

it('can remove evidence from test result', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $evidence = TestResultEvidence::factory()->create([
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Text->value,
        'content' => 'Test evidence',
    ]);

    $response = $this->delete(route('test-result.evidence.destroy', $evidence));

    $response->assertRedirect();

    $this->assertDatabaseMissing('test_result_evidence', [
        'id' => $evidence->id,
    ]);
});

it('removes image file when deleting image evidence', function () {
    Storage::fake('public');
    
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
        'status' => TestResultStatus::Passed,
    ]);

    $file = UploadedFile::fake()->image('test-image.jpg');
    $filePath = $file->store('test-evidence', 'public');

    $evidence = TestResultEvidence::factory()->create([
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Image->value,
        'file_path' => $filePath,
        'content' => $filePath,
    ]);

    $response = $this->delete(route('test-result.evidence.destroy', $evidence));

    $response->assertRedirect();

    $this->assertDatabaseMissing('test_result_evidence', [
        'id' => $evidence->id,
    ]);

    Storage::disk('public')->assertMissing($filePath);
});

it('shows validation errors for invalid evidence type', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => 'invalid_type',
        'content' => 'Test evidence',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('type');
});

it('shows validation errors for missing content', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => '', // Empty content
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('content');
});

it('requires authentication to add evidence', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => 'Test evidence',
    ]);

    $response->assertRedirect(route('login'));
});

it('requires authentication to remove evidence', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $evidence = TestResultEvidence::factory()->create([
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Text->value,
        'content' => 'Test evidence',
    ]);

    $response = $this->delete(route('test-result.evidence.destroy', $evidence));

    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent evidence deletion', function () {
    $this->actingAsUser();

    $response = $this->delete(route('test-result.evidence.destroy', 999));

    $response->assertNotFound();
});

it('shows success message after adding evidence', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $response = $this->post(route('test-result.evidence.store', $testResult), [
        'type' => EvidenceType::Text->value,
        'content' => 'Test evidence',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

it('shows success message after removing evidence', function () {
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
        'status' => TestResultStatus::Passed,
    ]);

    $evidence = TestResultEvidence::factory()->create([
        'test_result_id' => $testResult->id,
        'type' => EvidenceType::Text->value,
        'content' => 'Test evidence',
    ]);

    $response = $this->delete(route('test-result.evidence.destroy', $evidence));

    $response->assertRedirect();
    $response->assertSessionHas('success');
});