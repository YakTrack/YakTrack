<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Models\ProjectJiraIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class JiraRestClient
{
    public function __construct(
        private ProjectJiraIntegration $integration
    ) {
    }

    public static function verifyCredentials(string $siteHost, string $accountEmail, string $apiToken): void
    {
        $response = Http::withBasicAuth($accountEmail, $apiToken)
            ->acceptJson()
            ->get(self::apiBaseUrl($siteHost).'/myself');

        $response->throw();
    }

    /**
     * @return array<string, mixed>
     */
    public function getIssue(string $issueKey): array
    {
        $response = $this->http()->get('/issue/'.$issueKey, [
            'fields' => 'summary,description',
        ]);

        $response->throw();

        /** @var array<string, mixed> */
        return $response->json();
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth(
            $this->integration->account_email,
            $this->integration->api_token
        )
            ->acceptJson()
            ->baseUrl(self::apiBaseUrl($this->integration->site_host));
    }

    private static function apiBaseUrl(string $siteHost): string
    {
        $host = trim($siteHost);

        return 'https://'.$host.'/rest/api/3';
    }
}
