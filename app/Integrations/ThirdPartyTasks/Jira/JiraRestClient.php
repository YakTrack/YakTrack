<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use App\Models\ProjectJiraIntegration;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class JiraRestClient
{
    private const ISSUE_KEY_PATTERN = '/^[A-Z][A-Z0-9_]*-\d+$/i';

    /** @var list<int> */
    private const AUTHENTICATION_FAILURE_STATUSES = [401, 403];

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
     * @throws JiraAuthenticationException
     *
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    public function searchIssues(string $query): array
    {
        $issues = $this->pickerSuggestions($query);

        if (!self::looksLikeIssueKey($query)) {
            return $this->guardEmptyResults($issues);
        }

        $normalizedKey = Str::upper(trim($query));
        $pickerIndex = array_search($normalizedKey, array_column($issues, 'key'), true);

        if ($pickerIndex !== false) {
            return self::promote($issues, $pickerIndex);
        }

        $exactMatch = $this->findIssueByKey($normalizedKey);

        if ($exactMatch === null) {
            return $this->guardEmptyResults($issues);
        }

        array_unshift($issues, $exactMatch);

        return $issues;
    }

    /**
     * Jira answers an unauthenticated picker search with an empty 200 and an
     * unauthenticated issue read with a 404, so expired credentials are otherwise
     * indistinguishable from a genuine miss. Only an empty result set is worth the
     * extra round trip needed to tell the two apart.
     *
     * @param list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}> $issues
     *
     * @throws JiraAuthenticationException
     *
     * @return list<array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}>
     */
    private function guardEmptyResults(array $issues): array
    {
        if ($issues !== []) {
            return $issues;
        }

        $this->assertCredentialsAreAccepted();

        return $issues;
    }

    /**
     * A transport failure here is reported rather than raised, so an unreachable
     * Jira is never misreported to the user as expired credentials.
     *
     * @throws JiraAuthenticationException
     */
    private function assertCredentialsAreAccepted(): void
    {
        try {
            $response = $this->http()->get('/myself');
        } catch (HttpClientException $e) {
            report($e);

            return;
        }

        if (self::isAuthenticationFailure($response)) {
            throw new JiraAuthenticationException('Jira rejected the stored credentials.');
        }
    }

    private static function isAuthenticationFailure(Response $response): bool
    {
        return in_array($response->status(), self::AUTHENTICATION_FAILURE_STATUSES, true);
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
     * but never discards suggestions that already succeeded. Rejected credentials
     * are the exception: they are raised, because every later result would be empty.
     *
     * @throws JiraAuthenticationException
     *
     * @return array{key: string, summary: string, issue_type: ?string, avatar_url: ?string}|null
     */
    private function findIssueByKey(string $issueKey): ?array
    {
        try {
            $response = $this->http()->get('/issue/'.Str::upper($issueKey), [
                'fields' => 'summary,issuetype',
            ]);

            if (self::isAuthenticationFailure($response)) {
                throw new JiraAuthenticationException('Jira rejected the stored credentials.');
            }

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
     * @throws JiraAuthenticationException
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

        if (self::isAuthenticationFailure($response)) {
            throw new JiraAuthenticationException('Jira rejected the stored credentials.');
        }

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
