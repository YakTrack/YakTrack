<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Integrations\ThirdPartyTasks\ExternalTaskDriver;
use App\Integrations\ThirdPartyTasks\ExternalTaskIntegrationManager;
use App\Integrations\ThirdPartyTasks\Jira\JiraConnectionVerifier;
use App\Integrations\ThirdPartyTasks\Jira\JiraTaskFetcher;
use App\Models\Project;
use App\Models\ProjectJiraIntegration;

it('returns a jira verifier for the jira driver', function () {
    $manager = new ExternalTaskIntegrationManager;

    expect($manager->verifierFor(ExternalTaskDriver::Jira))->toBeInstanceOf(JiraConnectionVerifier::class);
});

it('returns a jira task fetcher when the project has a jira integration', function () {
    $project = Project::factory()->create();
    ProjectJiraIntegration::factory()->create(['project_id' => $project->id]);

    $manager = new ExternalTaskIntegrationManager;

    $fetcher = $manager->fetcherForProject($project->fresh());

    expect($fetcher)->toBeInstanceOf(JiraTaskFetcher::class);
});

it('returns null when the project has no external task integration', function () {
    $project = Project::factory()->create();

    $manager = new ExternalTaskIntegrationManager;

    expect($manager->fetcherForProject($project))->toBeNull();
});

it('reports jira as the driver when a jira integration exists', function () {
    $project = Project::factory()->create();
    ProjectJiraIntegration::factory()->create(['project_id' => $project->id]);

    $manager = new ExternalTaskIntegrationManager;

    expect($manager->driverForProject($project->fresh()))->toBe(ExternalTaskDriver::Jira);
});
