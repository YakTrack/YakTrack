<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Integrations\ThirdPartyTasks\Contracts\ExternalTaskFetcher;
use App\Integrations\ThirdPartyTasks\ExternalTaskPayload;
use App\Models\ProjectJiraIntegration;
use Illuminate\Support\Str;

final class JiraTaskFetcher implements ExternalTaskFetcher
{
    public function __construct(
        private ProjectJiraIntegration $integration
    ) {
    }

    public function fetch(string $reference): ExternalTaskPayload
    {
        $client = new JiraRestClient($this->integration);
        $issue = $client->getIssue($reference);

        /** @var array<string, mixed> $fields */
        $fields = $issue['fields'] ?? [];
        $summary = is_string($fields['summary'] ?? null) ? $fields['summary'] : 'Imported Jira issue';
        $description = JiraAdfPlainText::fromIssueField($fields['description'] ?? null);

        $normalizedInput = Str::upper($reference);
        $canonicalKey = is_string($issue['key'] ?? null) ? Str::upper($issue['key']) : $normalizedInput;

        return new ExternalTaskPayload(
            externalKey: $canonicalKey,
            title: $summary,
            description: $description,
        );
    }
}
