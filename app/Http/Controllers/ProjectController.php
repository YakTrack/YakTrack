<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\ProjectShowRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Show a list of projects.
     */
    public function index(ProjectIndexRequest $request): Response
    {
        $state = $request->tableState();

        $query = Project::query()
            ->notArchived()
            ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
            ->select('projects.*')
            ->with(['client:id,name'])
            ->withCount(['sprints', 'tasks', 'acceptanceCriteria', 'testRuns']);

        if ($state['q'] !== '') {
            $term = '%'.addcslashes($state['q'], '%_\\').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('projects.name', 'like', $term)
                    ->orWhere('projects.description', 'like', $term)
                    ->orWhere('clients.name', 'like', $term);
            });
        }

        if ($state['client_id'] !== null) {
            $query->where('projects.client_id', $state['client_id']);
        }

        match ($state['sort']) {
            'client' => $query
                ->orderBy('clients.name', $state['direction'])
                ->orderBy('projects.name'),
            default => $query->orderBy('projects.name', $state['direction']),
        };

        $projects = $query
            ->paginate($state['per_page'])
            ->withQueryString()
            ->through(fn (Project $project) => $project->append(['isDeletable', 'isArchived']));

        return Inertia::render('Project/Index', [
            'projects' => $projects,
            'clients'  => Client::query()->orderBy('name')->get(['id', 'name']),
            'table'    => [
                'filters' => [
                    'q'         => $state['q'],
                    'client_id' => $state['client_id'] !== null ? (string) $state['client_id'] : '',
                ],
                'sort'      => $state['sort'],
                'direction' => $state['direction'],
                'per_page'  => $state['per_page'],
            ],
        ]);
    }

    /**
     * Show the form for creating a new project.
     **/
    public function create(): Response
    {
        return Inertia::render('Project/Edit', [
            'clients' => Client::all(),
        ]);
    }

    /**
     * Save a new project.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name'             => 'required',
            'client_id'        => 'exists:clients,id',
            'task_code_prefix' => 'nullable|string|max:20',
        ]);

        $project = Project::create([
            'name'             => $request->input('name'),
            'description'      => $request->input('description'),
            'task_code_prefix' => $request->input('task_code_prefix'),
            'client_id'        => $request->input('client_id'),
        ]);

        return redirect()
            ->route('project.index')
            ->with('success', "Project \"$project->name\" created");
    }

    /**
     * Show a single project.
     */
    public function show(ProjectShowRequest $request, Project $project): Response
    {
        $tab = $request->validated('tab');

        $tasks = $project->tasks()
            ->with(['taskStatus'])
            ->orderBy('name')
            ->paginate(15, ['*'], 'tasks_page')
            ->withQueryString();

        $sessions = null;
        $sessionsTable = null;

        if ($tab === 'overview') {
            $state = $request->sessionsTableState();
            $sessions = $this->paginateProjectSessions($project, $state);
            $sessionsTable = [
                'filters' => [
                    'q'         => $state['q'],
                    'tab'       => 'overview',
                    'sprint_id' => $state['sprint_id'] !== null ? (string) $state['sprint_id'] : '',
                    'billable'  => $state['billable'] ?? '',
                ],
                'sort'      => $state['sort'],
                'direction' => $state['direction'],
                'per_page'  => $state['per_page'],
            ];
        }

        $project->load([
            'client',
            'jiraIntegration',
            'taskStatuses' => function ($query) {
                $query->withCount('tasks')->orderBy('sort_order');
            },
        ]);

        return Inertia::render('Project/Show', [
            'project'              => $project,
            'tab'                  => $tab,
            'tasks'                => $tasks,
            'sessions'             => $sessions,
            'sessionsTable'        => $sessionsTable,
            'sessionSprintFilters' => $project->sprints()->orderBy('name')->get(['id', 'name']),
            'jira'                 => [
                'connected' => $project->jiraIntegration !== null,
                'site_host' => $project->jiraIntegration?->site_host,
            ],
        ]);
    }

    /**
     * @param array{
     *     q: string,
     *     sprint_id: int|string|null,
     *     billable: string|null,
     *     sort: string,
     *     direction: string,
     *     per_page: int
     * } $state
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Session>
     */
    private function paginateProjectSessions(Project $project, array $state): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Session::query()
            ->select('sessions.*')
            ->join('tasks', 'sessions.task_id', '=', 'tasks.id')
            ->where('tasks.project_id', $project->id)
            ->whereNotNull('sessions.ended_at')
            ->leftJoin('sprints', 'sessions.sprint_id', '=', 'sprints.id')
            ->leftJoin('session_categories', 'sessions.session_category_id', '=', 'session_categories.id');

        if ($state['q'] !== '') {
            $term = '%'.addcslashes($state['q'], '%_\\').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('tasks.name', 'like', $term)
                    ->orWhere('sessions.comment', 'like', $term)
                    ->orWhere('sprints.name', 'like', $term)
                    ->orWhere('session_categories.name', 'like', $term);
            });
        }

        if ($state['sprint_id'] === 'none') {
            $query->whereNull('sessions.sprint_id');
        } elseif ($state['sprint_id'] !== null) {
            $query->where('sessions.sprint_id', (int) $state['sprint_id']);
        }

        $query = match ($state['billable']) {
            'yes'   => $query->where('sessions.is_billable', true),
            'no'    => $query->where('sessions.is_billable', false),
            default => $query,
        };

        $dir = $state['direction'];

        $query = match ($state['sort']) {
            'started_at' => $query->orderBy('sessions.started_at', $dir)->orderBy('sessions.id', 'desc'),
            'task'       => $query->orderBy('tasks.name', $dir)->orderBy('sessions.id', 'desc'),
            'sprint'     => $query->orderBy('sprints.name', $dir)->orderBy('sessions.id', 'desc'),
            'category'   => $query->orderBy('session_categories.name', $dir)->orderBy('sessions.id', 'desc'),
            'duration'   => $query->orderByRaw('TIMESTAMPDIFF(SECOND, sessions.started_at, sessions.ended_at) '.$dir)->orderBy('sessions.id', 'desc'),
            'billable'   => $query->orderBy('sessions.is_billable', $dir)->orderBy('sessions.id', 'desc'),
            default      => $query->orderBy('sessions.ended_at', $dir)->orderBy('sessions.id', 'desc'),
        };

        return $query
            ->with(['task', 'sessionCategory', 'sprint'])
            ->paginate($state['per_page'], ['*'], 'sessions_page')
            ->withQueryString();
    }

    /**
     * Show the kanban board for a project.
     */
    public function kanban(Project $project): Response
    {
        $tasks = $project->tasks()
            ->with('taskStatus')
            ->orderBy('created_at')
            ->get();

        return Inertia::render('Project/Kanban', [
            'project' => $project->load([
                'client',
                'taskStatuses' => function ($query) {
                    $query->where('is_closed', false)
                        ->withCount('tasks')
                        ->orderBy('sort_order');
                },
            ]),
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param Project $project
     */
    public function edit(Project $project): Response
    {
        return Inertia::render('Project/Edit', [
            'project' => $project,
            'clients' => Client::all(),
        ]);
    }

    /**
     * Update the specified project in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Project                  $project
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->validate($request, [
            'name'             => 'required',
            'client_id'        => 'exists:clients,id',
            'task_code_prefix' => 'nullable|string|max:20',
        ]);

        $project->update($request->all());

        return redirect()
            ->route('project.index')
            ->with('success', 'Project '.$project->name.' updated.');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     */
    public function destroy(Project $project): RedirectResponse
    {
        if (!$project->isDeletable()) {
            abort(422, 'Project is unable to be deleted');
        }

        $project->delete();

        return redirect()
            ->route('project.index')
            ->with('success', 'You have deleted Project '.$project->name.'.');
    }

    /**
     * Archive the specified project.
     */
    public function archive(Project $project): RedirectResponse
    {
        $project->archive();

        return redirect()
            ->route('project.index')
            ->with('success', "Project \"$project->name\" has been archived.");
    }

    /**
     * Unarchive the specified project.
     */
    public function unarchive(Project $project): RedirectResponse
    {
        $project->unarchive();

        return redirect()
            ->route('project.index')
            ->with('success', "Project \"$project->name\" has been unarchived.");
    }

    /**
     * Show archived projects.
     */
    public function archived(): Response
    {
        return Inertia::render('Project/Archived', [
            'projects' => Project::archived()
                ->orderBy('archived_at', 'desc')
                ->with('client')
                ->get()
                ->map
                ->append('isArchived'),
        ]);
    }
}
