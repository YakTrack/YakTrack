<?php

namespace App\Integrations\ThirdPartyTasks\Contracts;

use App\Integrations\ThirdPartyTasks\ExternalTaskPayload;

interface ExternalTaskFetcher
{
    /**
     * Load a single task from the third-party system (e.g. Jira issue, future: GitHub issue).
     *
     * @param string $reference Provider-specific reference (e.g. issue key).
     */
    public function fetch(string $reference): ExternalTaskPayload;
}
