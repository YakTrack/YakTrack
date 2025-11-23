<?php

use App\Models\AcceptanceCriteria;
use App\Models\Project;
use Illuminate\Http\UploadedFile;

it('can view the import page', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('acceptance-criteria.import'));

    $response->assertSuccessful();
    $response->assertSee($project->name);
});

it('can import acceptance criteria from a valid gherkin file', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();

    $gherkinContent = <<<'GHERKIN'
Feature: User Authentication
  As a user
  I want to be able to log in
  So that I can access my account

  Scenario: Successful login with valid credentials
    Given I am on the login page
    When I enter valid username and password
    Then I should be redirected to the dashboard

  Scenario: Failed login with invalid credentials
    Given I am on the login page
    When I enter invalid username and password
    Then I should see an error message
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => false,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that acceptance criteria were created
    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Successful login with valid credentials',
        'feature_id' => \App\Models\Feature::where('project_id', $project->id)->where('name', 'User Authentication')->first()->id,
        'is_active'  => true,
    ]);

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'Failed login with invalid credentials',
        'feature_id' => \App\Models\Feature::where('project_id', $project->id)->where('name', 'User Authentication')->first()->id,
        'is_active'  => true,
    ]);

    // Check that versions were created
    $criteria1 = AcceptanceCriteria::where('code', 'AC-001')->first();
    $criteria2 = AcceptanceCriteria::where('code', 'AC-002')->first();

    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $criteria1->id,
        'version_number'         => 1,
    ]);

    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $criteria2->id,
        'version_number'         => 1,
    ]);
});

it('can import acceptance criteria and overwrite existing ones', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    // Create existing acceptance criteria
    $existingCriteria = AcceptanceCriteria::factory()->create([
        'project_id'  => $project->id,
        'code'        => 'AC-001',
        'name'        => 'Old scenario name',
        'description' => 'Old description',
    ]);

    $existingCriteria->createVersion([
        'code'        => 'AC-001',
        'name'        => 'Old scenario name',
        'description' => 'Old description',
    ], auth()->id());

    $gherkinContent = <<<'GHERKIN'
Feature: User Authentication

  Scenario: New scenario name
    Given I am on the login page
    When I enter valid credentials
    Then I should be redirected to the dashboard
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => true,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that a new criteria was created with the updated name
    $newCriteria = AcceptanceCriteria::where('project_id', $project->id)
        ->where('name', 'New scenario name')
        ->first();

    expect($newCriteria)->not->toBeNull();
    expect($newCriteria->description)->toContain('I am on the login page');

    // Check that a version was created for the new criteria
    $this->assertDatabaseHas('acceptance_criteria_versions', [
        'acceptance_criteria_id' => $newCriteria->id,
        'name'                   => 'New scenario name',
        'version_number'         => 1,
    ]);
});

it('skips existing acceptance criteria when overwrite is false', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    // Create existing acceptance criteria
    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Existing scenario',
    ]);

    $gherkinContent = <<<'GHERKIN'
Feature: User Authentication

  Scenario: Existing scenario
    Given I am on the login page
    When I enter valid credentials
    Then I should be redirected to the dashboard

  Scenario: New scenario
    Given I am on the login page
    When I enter invalid credentials
    Then I should see an error message
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => false,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that only the new scenario was created
    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'New scenario',
    ]);

    // Check that the existing criteria was not updated
    $existingCriteria = AcceptanceCriteria::where('code', 'AC-001')->first();
    expect($existingCriteria->name)->toBe('Existing scenario');
});

it('validates required fields', function () {
    $this->actingAsUser();

    $response = $this->post(route('acceptance-criteria.import.process'), []);

    $response->assertSessionHasErrors(['project_id', 'file']);
});

it('validates project exists', function () {
    $this->actingAsUser();

    $file = UploadedFile::fake()->create('test.feature');

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id' => 999,
        'file'       => $file,
    ]);

    $response->assertSessionHasErrors(['project_id']);
});

it('validates file type', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf');

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id' => $project->id,
        'file'       => $file,
    ]);

    $response->assertSessionHasErrors(['file']);
});

it('validates gherkin file format', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $invalidGherkinContent = <<<'GHERKIN'
This is not a valid gherkin file
It doesn't have a Feature or Scenario
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $invalidGherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id' => $project->id,
        'file'       => $file,
    ]);

    $response->assertSessionHasErrors(['file']);
});

it('handles empty gherkin file', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $file = UploadedFile::fake()->createWithContent('test.feature', '');

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id' => $project->id,
        'file'       => $file,
    ]);

    $response->assertSessionHasErrors(['file']);
});

it('generates unique codes for multiple scenarios', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $gherkinContent = <<<'GHERKIN'
Feature: User Authentication

  Scenario: First scenario
    Given I am on the login page
    When I enter valid credentials
    Then I should be redirected

  Scenario: Second scenario
    Given I am on the login page
    When I enter invalid credentials
    Then I should see an error

  Scenario: Third scenario
    Given I am on the login page
    When I click forgot password
    Then I should see reset form
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => false,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that all scenarios were created with unique codes
    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'First scenario',
    ]);

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'Second scenario',
    ]);

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-003',
        'name'       => 'Third scenario',
    ]);
});

it('handles scenario outline correctly', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $gherkinContent = <<<'GHERKIN'
Feature: User Authentication

  Scenario Outline: Login with different credentials
    Given I am on the login page
    When I enter <username> and <password>
    Then I should see <result>

    Examples:
      | username | password | result |
      | valid    | valid    | success |
      | invalid  | valid    | error   |
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => false,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that scenario outline was created
    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Login with different credentials',
    ]);
});

it('handles complex gherkin with background and tags', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $gherkinContent = <<<'GHERKIN'
@authentication @smoke
Feature: User Authentication
  As a user
  I want to be able to log in
  So that I can access my account

  Background:
    Given the application is running
    And I am on the homepage

  @positive @login
  Scenario: Successful login with valid credentials
    Given I am on the login page
    When I enter valid username and password
    And I click the login button
    Then I should be redirected to the dashboard
    And I should see my profile information

  @negative @login
  Scenario: Failed login with invalid credentials
    Given I am on the login page
    When I enter invalid username and password
    And I click the login button
    Then I should see an error message
    And I should remain on the login page
GHERKIN;

    $file = UploadedFile::fake()->createWithContent('test.feature', $gherkinContent);

    $response = $this->post(route('acceptance-criteria.import.process'), [
        'project_id'         => $project->id,
        'file'               => $file,
        'overwrite_existing' => false,
    ]);

    $response->assertRedirect(route('acceptance-criteria.index'));

    // Check that both scenarios were created
    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Successful login with valid credentials',
    ]);

    $this->assertDatabaseHas('acceptance_criteria', [
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'Failed login with invalid credentials',
    ]);

    // Check that descriptions include all steps
    $successCriteria = AcceptanceCriteria::where('code', 'AC-001')->first();
    expect($successCriteria->description)->toContain('I am on the login page');
    expect($successCriteria->description)->toContain('I enter valid username and password');
    expect($successCriteria->description)->toContain('I click the login button');
    expect($successCriteria->description)->toContain('I should be redirected to the dashboard');
});

it('can group acceptance criteria by feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    // Create features
    $authFeature = \App\Models\Feature::create([
        'project_id' => $project->id,
        'name'       => 'User Authentication',
        'is_active'  => true,
    ]);

    $regFeature = \App\Models\Feature::create([
        'project_id' => $project->id,
        'name'       => 'User Registration',
        'is_active'  => true,
    ]);

    // Create acceptance criteria with different features
    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Login scenario',
        'feature_id' => $authFeature->id,
    ]);

    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'Registration scenario',
        'feature_id' => $regFeature->id,
    ]);

    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-003',
        'name'       => 'Another login scenario',
        'feature_id' => $authFeature->id,
    ]);

    // Test grouping by feature
    $groupedCriteria = AcceptanceCriteria::getGroupedByFeature($project->id);

    expect($groupedCriteria)->toHaveKeys(['User Authentication', 'User Registration']);
    expect($groupedCriteria['User Authentication'])->toHaveCount(2);
    expect($groupedCriteria['User Registration'])->toHaveCount(1);

    // Test getting features for project
    $features = AcceptanceCriteria::getFeaturesForProject($project->id);
    expect($features)->toHaveCount(2);
    expect($features->pluck('name'))->toContain('User Authentication');
    expect($features->pluck('name'))->toContain('User Registration');
});

it('can filter acceptance criteria by feature', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    // Create features
    $authFeature = \App\Models\Feature::create([
        'project_id' => $project->id,
        'name'       => 'User Authentication',
        'is_active'  => true,
    ]);

    $regFeature = \App\Models\Feature::create([
        'project_id' => $project->id,
        'name'       => 'User Registration',
        'is_active'  => true,
    ]);

    // Create acceptance criteria with different features
    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-001',
        'name'       => 'Login scenario',
        'feature_id' => $authFeature->id,
    ]);

    AcceptanceCriteria::factory()->create([
        'project_id' => $project->id,
        'code'       => 'AC-002',
        'name'       => 'Registration scenario',
        'feature_id' => $regFeature->id,
    ]);

    // Test filtering by feature
    $response = $this->get(route('acceptance-criteria.index', [
        'project_id' => $project->id,
        'feature_id' => $authFeature->id,
    ]));

    $response->assertSuccessful();
    $response->assertSee('Login scenario');
    $response->assertDontSee('Registration scenario');
});
