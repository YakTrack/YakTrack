<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Models\ProjectJiraIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    /**
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    public function searchIssues(string $query): array
    {
        $response = $this->http()->get('/issue/picker', [
            'query'      => $query,
            'showAvatar' => true,
        ]);

        $response->throw();

        /** @var array<string, mixed> */
        $data = $response->json();

        $issues = [];
        $seen = [];

        foreach ($data['sections'] ?? [] as $section) {
            if (! is_array($section)) {
                continue;
            }

            foreach ($section['issues'] ?? [] as $issue) {
                if (! is_array($issue)) {
                    continue;
                }

                $key = is_string($issue['key'] ?? null) ? Str::upper($issue['key']) : null;
                if ($key === null || isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;

                $summary = is_string($issue['summaryText'] ?? null)
                    ? $issue['summaryText']
                    : (is_string($issue['summary'] ?? null) ? $issue['summary'] : '');

                $issueType = null;
                if (is_array($issue['issueType'] ?? null) && is_string($issue['issueType']['name'] ?? null)) {
                    $issueType = $issue['issueType']['name'];
                }

                $avatarUrl = is_string($issue['avatarUrl'] ?? null) ? $issue['avatarUrl'] : null;

                $issues[] = [
                    'key'        => $key,
                    'summary'    => $summary,
                    'issue_type' => $issueType,
                    'avatar_url' => $avatarUrl,
                ];
            }
        }

        return $issues;
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
