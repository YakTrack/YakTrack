<?php

use App\Models\Project;
use App\Models\Task;

it('can view the page to create a task', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    Task::factory()->create([
        'project_id' => $project->id,
    ]);

    $response = $this->get(route('task.create'));

    $response->assertSuccessful();

    $response->assertSee($project->name);
});

it('passes prefill project id when project_id query matches a non-archived project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->get(route('task.create', ['project_id' => $project->id]));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Task/Create')
            ->where('prefill_project_id', $project->id)
    );
});

it('does not prefill when project_id query refers to an archived project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create(['archived_at' => now()]);

    $response = $this->get(route('task.create', ['project_id' => $project->id]));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Task/Create')
            ->where('prefill_project_id', null)
    );
});

it('does not prefill when project_id query is invalid', function () {
    $this->actingAsUser();

    $response = $this->get(route('task.create', ['project_id' => 999_999]));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Task/Create')
            ->where('prefill_project_id', null)
    );
});

it('includes jira integration data on projects when creating a task', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    \App\Models\ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
    ]);

    $response = $this->get(route('task.create'));

    $response->assertSuccessful();
    $response->assertInertia(
        fn ($page) => $page
            ->component('Task/Create')
            ->has('projects', 1)
            ->where('projects.0.id', $project->id)
            ->where('projects.0.jira_integration.site_host', 'acme.atlassian.net')
    );
});

it('can create a task linked to a jira issue', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('task.store'), [
        'name'           => 'KEY-1: Fix the bug',
        'description'    => 'Detailed description here.',
        'project_id'     => $project->id,
        'jira_issue_key' => 'KEY-1',
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', [
        'name'           => 'KEY-1: Fix the bug',
        'description'    => 'Detailed description here.',
        'project_id'     => $project->id,
        'jira_issue_key' => 'KEY-1',
    ]);
});

it('can submit a post request to create a task', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    $project = Project::factory()->create();

    $response = $this->post(route('task.store'), [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
    ]);
});

it('can submit a post request to create a task with required fields only', function () {
    $this->withoutExceptionHandling();

    $this->actingAsUser();

    Project::factory()->create();

    $response = $this->post(route('task.store'), [
        'name' => 'Test Task',
    ]);

    $response->assertRedirect(route('task.index'));

    $this->assertDatabaseHas('tasks', [
        'name'        => 'Test Task',
        'description' => '',
        'project_id'  => null,
    ]);
});

it('cannot create a duplicate named task for a project', function () {
    $this->actingAsUser();

    $project = Project::factory()->create();
    Task::factory()->create([
        'name'       => 'Test Task',
        'project_id' => $project->id,
    ]);

    $response = $this->post(route('task.store'), [
        'name'        => 'Test Task',
        'description' => 'Test task description.',
        'project_id'  => $project->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrorsIn('name');

    $this->assertDatabaseMissing('tasks', [
        'description' => 'Test task description.',
    ]);
});
