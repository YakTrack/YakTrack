<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectIndexRequest;
use App\Models\Client;
use App\Models\Project;
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
    public function show(Project $project): Response
    {
        $sessions = $project->sessions()
            ->with(['task', 'sessionCategory'])
            ->whereNotNull('ended_at')
            ->orderBy('ended_at', 'desc')
            ->paginate(15);

        $tasks = $project->tasks()
            ->with(['taskStatus'])
            ->orderBy('name')
            ->paginate(15, ['*'], 'tasks_page');

        return Inertia::render('Project/Show', [
            'project' => $project->load([
                'client',
                'taskStatuses' => function ($query) {
                    $query->withCount('tasks')->orderBy('sort_order');
                },
            ]),
            'sessions' => $sessions,
            'tasks'    => $tasks,
        ]);
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
