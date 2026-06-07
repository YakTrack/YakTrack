<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportJiraIssueRequest;
use App\Http\Requests\SearchJiraIssuesRequest;
use App\Http\Requests\StoreProjectJiraIntegrationRequest;
use App\Integrations\ThirdPartyTasks\ExternalTaskDriver;
use App\Integrations\ThirdPartyTasks\ExternalTaskIntegrationManager;
use App\Integrations\ThirdPartyTasks\Jira\JiraRestClient;
use App\Models\Project;
use App\Models\ProjectJiraIntegration;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ProjectJiraController extends Controller
{
    public function __construct(
        private ExternalTaskIntegrationManager $externalTaskIntegrations
    ) {
    }

    private function redirectToProjectTab(Project $project, string $tab): RedirectResponse
    {
        return redirect()->to(route('project.show', $project).'?tab='.urlencode($tab));
    }

    public function store(StoreProjectJiraIntegrationRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->externalTaskIntegrations->verifierFor(ExternalTaskDriver::Jira)->verify([
                'site_host'     => $validated['site_host'],
                'account_email' => $validated['account_email'],
                'api_token'     => $validated['api_token'],
            ]);
        } catch (RequestException) {
            return $this->redirectToProjectTab($project, 'integrations')
                ->withErrors([
                    'site_host' => 'We could not verify these Jira credentials. Check the site host, email, and API token.',
                ]);
        }

        ProjectJiraIntegration::updateOrCreate(
            ['project_id' => $project->id],
            [
                'site_host'     => $validated['site_host'],
                'account_email' => $validated['account_email'],
                'api_token'     => $validated['api_token'],
            ]
        );

        return $this->redirectToProjectTab($project, 'integrations')
            ->with('success', 'Jira is connected to this project.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->jiraIntegration?->delete();

        return $this->redirectToProjectTab($project, 'integrations')
            ->with('success', 'Jira has been disconnected from this project.');
    }

    public function searchIssues(SearchJiraIssuesRequest $request, Project $project): JsonResponse
    {
        $integration = $project->jiraIntegration;

        if ($integration === null) {
            return response()->json([
                'message' => 'Connect Jira to this project before searching issues.',
            ], 422);
        }

        try {
            $client = new JiraRestClient($integration);
            $issues = $client->searchIssues($request->validated('q'));
        } catch (RequestException $e) {
            report($e);

            return response()->json([
                'message' => 'Could not search Jira issues. Try again later.',
            ], 502);
        }

        $importedKeys = Task::query()
            ->where('project_id', $project->id)
            ->whereNotNull('jira_issue_key')
            ->pluck('jira_issue_key')
            ->map(fn (string $key): string => Str::upper($key))
            ->flip();

        $issues = array_map(function (array $issue) use ($importedKeys): array {
            $issue['already_imported'] = $importedKeys->has($issue['key']);

            return $issue;
        }, $issues);

        return response()->json(['issues' => $issues]);
    }

    public function previewIssue(ImportJiraIssueRequest $request, Project $project): JsonResponse
    {
        $fetcher = $this->externalTaskIntegrations->fetcherForProject($project);

        if ($fetcher === null) {
            return response()->json([
                'message' => 'Connect Jira to this project before previewing issues.',
            ], 422);
        }

        $issueKeyInput = $request->validated('issue_key');
        $normalizedKey = Str::upper($issueKeyInput);

        $alreadyImported = Task::query()
            ->where('project_id', $project->id)
            ->where('jira_issue_key', $normalizedKey)
            ->exists();

        try {
            $payload = $fetcher->fetch($issueKeyInput);
        } catch (RequestException $e) {
            if ($e->response !== null && $e->response->status() === 404) {
                return response()->json([
                    'message' => 'That Jira issue could not be found.',
                ], 404);
            }

            report($e);

            return response()->json([
                'message' => 'Could not load that issue from Jira. Try again later.',
            ], 502);
        }

        $description = $payload->description;
        if (strlen($description) > 500) {
            $description = Str::limit($description, 497, '…');
        }

        return response()->json([
            'issue' => [
                'key'              => $payload->externalKey,
                'summary'          => $payload->title,
                'description'      => $description,
                'already_imported' => $alreadyImported,
            ],
        ]);
    }

    public function import(ImportJiraIssueRequest $request, Project $project): RedirectResponse
    {
        $fetcher = $this->externalTaskIntegrations->fetcherForProject($project);

        if ($fetcher === null) {
            return $this->redirectToProjectTab($project, 'integrations')
                ->withErrors([
                    'issue_key' => 'Connect Jira to this project before importing issues.',
                ]);
        }

        $issueKeyInput = $request->validated('issue_key');
        $normalizedKey = Str::upper($issueKeyInput);

        if (Task::query()->where('project_id', $project->id)->where('jira_issue_key', $normalizedKey)->exists()) {
            return $this->redirectToProjectTab($project, 'integrations')
                ->withErrors([
                    'issue_key' => 'This Jira issue has already been imported.',
                ]);
        }

        try {
            $payload = $fetcher->fetch($issueKeyInput);
        } catch (RequestException $e) {
            if ($e->response !== null && $e->response->status() === 404) {
                return $this->redirectToProjectTab($project, 'integrations')
                    ->withErrors([
                        'issue_key' => 'That Jira issue could not be found.',
                    ]);
            }

            report($e);

            return $this->redirectToProjectTab($project, 'integrations')
                ->withErrors([
                    'issue_key' => 'Could not load that issue from Jira. Try again later.',
                ]);
        }

        if (Task::query()->where('project_id', $project->id)->where('jira_issue_key', $payload->externalKey)->exists()) {
            return $this->redirectToProjectTab($project, 'integrations')
                ->withErrors([
                    'issue_key' => 'This Jira issue has already been imported.',
                ]);
        }

        $name = sprintf('%s: %s', $payload->externalKey, $payload->title);
        if (strlen($name) > 255) {
            $name = Str::limit($name, 252, '…');
        }

        $statusId = TaskStatus::query()
            ->where('project_id', $project->id)
            ->where('is_default', true)
            ->value('id');

        if ($statusId === null) {
            $statusId = TaskStatus::query()
                ->where('project_id', $project->id)
                ->orderBy('sort_order')
                ->value('id');
        }

        Task::create([
            'name'           => $name,
            'description'    => $payload->description,
            'project_id'     => $project->id,
            'status_id'      => $statusId,
            'status'         => 'incomplete',
            'jira_issue_key' => $payload->externalKey,
        ]);

        return $this->redirectToProjectTab($project, 'tasks')
            ->with('success', sprintf('Created task from Jira issue %s.', $payload->externalKey));
    }
}
