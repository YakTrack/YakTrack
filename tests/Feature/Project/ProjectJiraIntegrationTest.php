<?php

use App\Models\Project;
use App\Models\ProjectJiraIntegration;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Http;

it('stores jira credentials when the jira api accepts them', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/myself' => Http::response(['displayName' => 'Dev'], 200),
    ]);

    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->post(route('project.jira.store', $project), [
        'site_host' => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token' => 'token-token-token-token',
    ]);

    $response->assertRedirect(route('project.show', $project));

    $this->assertDatabaseHas('project_jira_integrations', [
        'project_id' => $project->id,
        'site_host' => 'acme.atlassian.net',
    ]);

    expect(ProjectJiraIntegration::query()->where('project_id', $project->id)->exists())->toBeTrue();
});

it('rejects jira credentials when verification fails', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/myself' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->from(route('project.show', $project))->post(route('project.jira.store', $project), [
        'site_host' => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token' => 'token-token-token-token',
    ]);

    $response->assertRedirect(route('project.show', $project));
    $response->assertSessionHasErrors('site_host');

    expect(ProjectJiraIntegration::query()->where('project_id', $project->id)->exists())->toBeFalse();
});

it('imports a jira issue as a task', function () {
    Http::fake(function (\Illuminate\Http\Client\Request $request) {
        if (str_contains($request->url(), '/myself')) {
            return Http::response(['displayName' => 'Dev'], 200);
        }

        if (str_contains($request->url(), '/issue/KEY-1')) {
            return Http::response([
                'key' => 'KEY-1',
                'fields' => [
                    'summary' => 'Fix the bug',
                    'description' => null,
                ],
            ], 200);
        }

        return Http::response([], 500);
    });

    $project = Project::factory()->create();
    TaskStatus::factory()->default()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $this->post(route('project.jira.store', $project), [
        'site_host' => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token' => 'token-token-token-token',
    ]);

    $response = $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response->assertRedirect(route('project.show', $project));

    $task = Task::query()->where('project_id', $project->id)->first();
    expect($task)->not->toBeNull();
    expect($task->jira_issue_key)->toBe('KEY-1');
    expect($task->name)->toBe('KEY-1: Fix the bug');
});

it('does not import the same jira issue twice', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/myself' => Http::response(['displayName' => 'Dev'], 200),
        'https://acme.atlassian.net/rest/api/3/issue/*' => Http::response([
            'key' => 'KEY-1',
            'fields' => [
                'summary' => 'Fix the bug',
                'description' => null,
            ],
        ], 200),
    ]);

    $project = Project::factory()->create();
    TaskStatus::factory()->default()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $this->post(route('project.jira.store', $project), [
        'site_host' => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token' => 'token-token-token-token',
    ]);

    $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response = $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response->assertRedirect(route('project.show', $project));
    $response->assertSessionHasErrors('issue_key');

    expect(Task::query()->where('project_id', $project->id)->count())->toBe(1);
});

it('includes jira connection props on the project page', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    expect($response->props()['jira']['connected'])->toBeFalse();

    ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host' => 'acme.atlassian.net',
    ]);

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    expect($response->props()['jira']['connected'])->toBeTrue();
    expect($response->props()['jira']['site_host'])->toBe('acme.atlassian.net');
});
