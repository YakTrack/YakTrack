<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportJiraIssueRequest;
use App\Http\Requests\StoreProjectJiraIntegrationRequest;
use App\Integrations\ThirdPartyTasks\ExternalTaskDriver;
use App\Integrations\ThirdPartyTasks\ExternalTaskIntegrationManager;
use App\Models\Project;
use App\Models\ProjectJiraIntegration;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ProjectJiraController extends Controller
{
    public function __construct(
        private ExternalTaskIntegrationManager $externalTaskIntegrations
    ) {}

    private function redirectToProjectTab(Project $project, string $tab): RedirectResponse
    {
        return redirect()->to(route('project.show', $project).'?tab='.urlencode($tab));
    }

    public function store(StoreProjectJiraIntegrationRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->externalTaskIntegrations->verifierFor(ExternalTaskDriver::Jira)->verify([
                'site_host' => $validated['site_host'],
                'account_email' => $validated['account_email'],
                'api_token' => $validated['api_token'],
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
                'site_host' => $validated['site_host'],
                'account_email' => $validated['account_email'],
                'api_token' => $validated['api_token'],
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
            'name' => $name,
            'description' => $payload->description,
            'project_id' => $project->id,
            'status_id' => $statusId,
            'status' => 'incomplete',
            'jira_issue_key' => $payload->externalKey,
        ]);

        return $this->redirectToProjectTab($project, 'tasks')
            ->with('success', sprintf('Created task from Jira issue %s.', $payload->externalKey));
    }
}
