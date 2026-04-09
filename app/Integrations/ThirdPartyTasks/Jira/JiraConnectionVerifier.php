<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Integrations\ThirdPartyTasks\Contracts\VerifiesExternalTaskConnection;
use InvalidArgumentException;

final class JiraConnectionVerifier implements VerifiesExternalTaskConnection
{
    /**
     * @param  array<string, string>  $credentials  Keys: site_host, account_email, api_token
     */
    public function verify(array $credentials): void
    {
        $siteHost = $credentials['site_host'] ?? null;
        $accountEmail = $credentials['account_email'] ?? null;
        $apiToken = $credentials['api_token'] ?? null;

        if (! is_string($siteHost) || ! is_string($accountEmail) || ! is_string($apiToken)) {
            throw new InvalidArgumentException('Jira verification requires site_host, account_email, and api_token.');
        }

        JiraRestClient::verifyCredentials($siteHost, $accountEmail, $apiToken);
    }
}
