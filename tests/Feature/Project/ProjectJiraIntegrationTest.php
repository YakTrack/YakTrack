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
        'site_host'     => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token'     => 'token-token-token-token',
    ]);

    $response->assertRedirect(route('project.show', $project).'?tab=integrations');

    $this->assertDatabaseHas('project_jira_integrations', [
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
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
        'site_host'     => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token'     => 'token-token-token-token',
    ]);

    $response->assertRedirect(route('project.show', $project).'?tab=integrations');
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
                'key'    => 'KEY-1',
                'fields' => [
                    'summary'     => 'Fix the bug',
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
        'site_host'     => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token'     => 'token-token-token-token',
    ]);

    $response = $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response->assertRedirect(route('project.show', $project).'?tab=tasks');

    $task = Task::query()->where('project_id', $project->id)->first();
    expect($task)->not->toBeNull();
    expect($task->jira_issue_key)->toBe('KEY-1');
    expect($task->name)->toBe('KEY-1: Fix the bug');
});

it('does not import the same jira issue twice', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/myself'  => Http::response(['displayName' => 'Dev'], 200),
        'https://acme.atlassian.net/rest/api/3/issue/*' => Http::response([
            'key'    => 'KEY-1',
            'fields' => [
                'summary'     => 'Fix the bug',
                'description' => null,
            ],
        ], 200),
    ]);

    $project = Project::factory()->create();
    TaskStatus::factory()->default()->create(['project_id' => $project->id]);

    $this->actingAsUser();

    $this->post(route('project.jira.store', $project), [
        'site_host'     => 'acme.atlassian.net',
        'account_email' => 'dev@example.com',
        'api_token'     => 'token-token-token-token',
    ]);

    $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response = $this->post(route('project.jira.import', $project), [
        'issue_key' => 'KEY-1',
    ]);

    $response->assertRedirect(route('project.show', $project).'?tab=integrations');
    $response->assertSessionHasErrors('issue_key');

    expect(Task::query()->where('project_id', $project->id)->count())->toBe(1);
});

it('searches jira issues for autocomplete', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/issue/picker*' => Http::response([
            'sections' => [
                [
                    'id'     => 'cs',
                    'label'  => 'Current Search',
                    'issues' => [
                        [
                            'id'          => 10001,
                            'key'         => 'KEY-1',
                            'summary'     => 'Fix the bug',
                            'summaryText' => 'Fix the bug',
                            'issueType'   => ['name' => 'Bug'],
                            'avatarUrl'   => 'https://acme.atlassian.net/icon.png',
                        ],
                        [
                            'id'          => 10002,
                            'key'         => 'KEY-2',
                            'summary'     => 'Already done',
                            'summaryText' => 'Already done',
                            'issueType'   => ['name' => 'Task'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $project = Project::factory()->create();
    TaskStatus::factory()->default()->create(['project_id' => $project->id]);

    ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
    ]);

    Task::factory()->create([
        'project_id'     => $project->id,
        'status_id'      => TaskStatus::query()->where('project_id', $project->id)->value('id'),
        'jira_issue_key' => 'KEY-2',
    ]);

    $this->actingAsUser();

    $response = $this->getJson(route('project.jira.issues.search', $project).'?q=KEY');

    $response->assertSuccessful();
    $response->assertJsonPath('issues.0.key', 'KEY-1');
    $response->assertJsonPath('issues.0.summary', 'Fix the bug');
    $response->assertJsonPath('issues.0.issue_type', 'Bug');
    $response->assertJsonPath('issues.0.already_imported', false);
    $response->assertJsonPath('issues.1.key', 'KEY-2');
    $response->assertJsonPath('issues.1.already_imported', true);
});

it('previews a jira issue before import', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/issue/KEY-1*' => Http::response([
            'key'    => 'KEY-1',
            'fields' => [
                'summary'     => 'Fix the bug',
                'description' => [
                    'type'    => 'doc',
                    'version' => 1,
                    'content' => [
                        [
                            'type'    => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Detailed description here.'],
                            ],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $project = Project::factory()->create();

    ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
    ]);

    $this->actingAsUser();

    $response = $this->getJson(route('project.jira.issues.preview', $project).'?issue_key=KEY-1');

    $response->assertSuccessful();
    $response->assertJsonPath('issue.key', 'KEY-1');
    $response->assertJsonPath('issue.summary', 'Fix the bug');
    $response->assertJsonPath('issue.description', 'Detailed description here.');
    $response->assertJsonPath('issue.name', 'KEY-1: Fix the bug');
    $response->assertJsonPath('issue.already_imported', false);
});

it('excludes the current task when checking if a jira issue is already linked', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/issue/picker*' => Http::response([
            'sections' => [
                [
                    'id'     => 'cs',
                    'label'  => 'Current Search',
                    'issues' => [
                        [
                            'id'          => 10001,
                            'key'         => 'KEY-1',
                            'summary'     => 'Fix the bug',
                            'summaryText' => 'Fix the bug',
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $project = Project::factory()->create();
    TaskStatus::factory()->default()->create(['project_id' => $project->id]);

    ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
    ]);

    $task = Task::factory()->create([
        'project_id'     => $project->id,
        'status_id'      => TaskStatus::query()->where('project_id', $project->id)->value('id'),
        'jira_issue_key' => 'KEY-1',
    ]);

    $this->actingAsUser();

    $response = $this->getJson(
        route('project.jira.issues.search', $project).'?q=KEY&exclude_task_id='.$task->id
    );

    $response->assertSuccessful();
    $response->assertJsonPath('issues.0.key', 'KEY-1');
    $response->assertJsonPath('issues.0.already_imported', false);
});

it('rejects jira issue search when jira is not connected', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->getJson(route('project.jira.issues.search', $project).'?q=KEY');

    $response->assertUnprocessable();
    $response->assertJsonPath('message', 'Connect Jira to this project before searching issues.');
});

it('includes jira connection props on the project page', function () {
    $project = Project::factory()->create();

    $this->actingAsUser();

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    expect($response->props()['jira']['connected'])->toBeFalse();

    ProjectJiraIntegration::factory()->create([
        'project_id' => $project->id,
        'site_host'  => 'acme.atlassian.net',
    ]);

    $response = $this->get(route('project.show', $project));

    $response->assertSuccessful();
    expect($response->props()['jira']['connected'])->toBeTrue();
    expect($response->props()['jira']['site_host'])->toBe('acme.atlassian.net');
});
