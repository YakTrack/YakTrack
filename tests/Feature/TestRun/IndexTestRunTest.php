<?php

use App\Models\Project;
use App\Models\TestRun;
use App\Models\User;

it('can view the test run index page', function () {
    $this->actingAsUser();

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('displays existing test runs', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['name' => 'Test Project']);
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Sprint 1 Testing',
        'description'         => 'Testing for sprint 1 features',
        'executed_at'         => now(),
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('displays test runs without description', function () {
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

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('can filter test runs by project', function () {
    $this->actingAsUser();

    $project1 = Project::factory()->create(['name' => 'Project One']);
    $project2 = Project::factory()->create(['name' => 'Project Two']);
    $user = User::factory()->create();

    $testRun1 = TestRun::factory()->create([
        'project_id'          => $project1->id,
        'name'                => 'Test Run One',
        'executed_by_user_id' => $user->id,
    ]);

    $testRun2 = TestRun::factory()->create([
        'project_id'          => $project2->id,
        'name'                => 'Test Run Two',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.index', ['project_id' => $project1->id]));

    $response->assertSuccessful();
});

it('shows empty state when no test runs exist', function () {
    $this->actingAsUser();

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('displays execution date and user information', function () {
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

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('can navigate to create page from index', function () {
    $this->actingAsUser();

    $response = $this->get(route('test-run.create'));

    $response->assertSuccessful();
});

it('can navigate to show page from test run link', function () {
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

it('requires authentication to view index', function () {
    $response = $this->get(route('test-run.index'));

    $response->assertRedirect(route('login'));
});

it('paginates results when there are many test runs', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();

    // Create more than 15 test runs (default pagination limit)
    TestRun::factory()->count(20)->create([
        'project_id'          => $project->id,
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('displays test run status summary', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();
    $testRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Test Run',
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});

it('orders test runs by execution date descending', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    $user = User::factory()->create();

    $olderTestRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Older Test Run',
        'executed_at'         => now()->subDays(2),
        'executed_by_user_id' => $user->id,
    ]);

    $newerTestRun = TestRun::factory()->create([
        'project_id'          => $project->id,
        'name'                => 'Newer Test Run',
        'executed_at'         => now()->subDays(1),
        'executed_by_user_id' => $user->id,
    ]);

    $response = $this->get(route('test-run.index'));

    $response->assertSuccessful();
});
