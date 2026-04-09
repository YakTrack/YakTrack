<?php

namespace App\Integrations\ThirdPartyTasks;

use App\Integrations\ThirdPartyTasks\Contracts\ExternalTaskFetcher;
use App\Integrations\ThirdPartyTasks\Contracts\VerifiesExternalTaskConnection;
use App\Integrations\ThirdPartyTasks\Jira\JiraConnectionVerifier;
use App\Integrations\ThirdPartyTasks\Jira\JiraTaskFetcher;
use App\Models\Project;

final class ExternalTaskIntegrationManager
{
    public function verifierFor(ExternalTaskDriver $driver): VerifiesExternalTaskConnection
    {
        return match ($driver) {
            ExternalTaskDriver::Jira => new JiraConnectionVerifier(),
        };
    }

    public function fetcherForProject(Project $project): ?ExternalTaskFetcher
    {
        if ($project->jiraIntegration !== null) {
            return new JiraTaskFetcher($project->jiraIntegration);
        }

        return null;
    }

    public function driverForProject(Project $project): ?ExternalTaskDriver
    {
        if ($project->jiraIntegration !== null) {
            return ExternalTaskDriver::Jira;
        }

        return null;
    }
}
