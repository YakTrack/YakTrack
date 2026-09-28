<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Models\ProjectJiraIntegration;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class JiraRestClient
{
    private const ISSUE_KEY_PATTERN = '/^[A-Z][A-Z0-9_]*-\d+$/i';

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
     * Whether the query is shaped like a Jira issue key, such as ABC-123.
     */
    public static function looksLikeIssueKey(string $query): bool
    {
        return preg_match(self::ISSUE_KEY_PATTERN, trim($query)) === 1;
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
     * Search issues for autocomplete. When the query is shaped like an issue key the
     * issue is also looked up directly, because the picker endpoint only suggests
     * issues from the connected account's recent history and can miss exact keys.
     *
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    public function searchIssues(string $query): array
    {
        $issues = $this->pickerSuggestions($query);

        if (!self::looksLikeIssueKey($query)) {
            return $issues;
        }

        $normalizedKey = Str::upper(trim($query));
        $pickerIndex = array_search($normalizedKey, array_column($issues, 'key'), true);

        if ($pickerIndex !== false) {
            return self::promote($issues, $pickerIndex);
        }

        $exactMatch = $this->findIssueByKey($normalizedKey);

        if ($exactMatch === null) {
            return $issues;
        }

        array_unshift($issues, $exactMatch);

        return $issues;
    }

    /**
     * @param list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}> $issues
     *
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    private static function promote(array $issues, int $index): array
    {
        $match = $issues[$index];
        unset($issues[$index]);

        return [$match, ...array_values($issues)];
    }

    /**
     * The direct lookup supplements the picker, so a failure here is reported
     * but never discards suggestions that already succeeded.
     *
     * @return array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}|null
     */
    private function findIssueByKey(string $issueKey): ?array
    {
        try {
            $response = $this->http()->get('/issue/'.Str::upper($issueKey), [
                'fields' => 'summary,issuetype',
            ]);

            if ($response->status() === 404) {
                return null;
            }

            $response->throw();
        } catch (HttpClientException $e) {
            report($e);

            return null;
        }

        /** @var array<string, mixed> $issue */
        $issue = $response->json();

        $key = is_string($issue['key'] ?? null) ? Str::upper($issue['key']) : null;

        if ($key === null) {
            return null;
        }

        /** @var array<string, mixed> $fields */
        $fields = is_array($issue['fields'] ?? null) ? $issue['fields'] : [];

        $issueType = null;
        $avatarUrl = null;

        if (is_array($fields['issuetype'] ?? null)) {
            $issueType = is_string($fields['issuetype']['name'] ?? null) ? $fields['issuetype']['name'] : null;
            $avatarUrl = is_string($fields['issuetype']['iconUrl'] ?? null) ? $fields['issuetype']['iconUrl'] : null;
        }

        return [
            'key'        => $key,
            'summary'    => is_string($fields['summary'] ?? null) ? $fields['summary'] : '',
            'issue_type' => $issueType,
            'avatar_url' => $avatarUrl,
        ];
    }

    /**
     * Picker suggestions carry the issue type icon as `img` and no issue type name,
     * so `issue_type` is only populated by the direct lookup.
     *
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    private function pickerSuggestions(string $query): array
    {
        $response = $this->http()->get('/issue/picker', [
            'query'        => $query,
            'showAvatar'   => 'true',
            'showSubTasks' => 'true',
        ]);

        $response->throw();

        /** @var array<string, mixed> */
        $data = $response->json();

        $issues = [];
        $seen = [];

        foreach ($data['sections'] ?? [] as $section) {
            if (!is_array($section)) {
                continue;
            }

            foreach ($section['issues'] ?? [] as $issue) {
                if (!is_array($issue)) {
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

                $issues[] = [
                    'key'        => $key,
                    'summary'    => $summary,
                    'issue_type' => null,
                    'avatar_url' => is_string($issue['img'] ?? null) ? $issue['img'] : null,
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
