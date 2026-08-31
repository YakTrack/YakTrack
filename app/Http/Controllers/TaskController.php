<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAssignTasksToProjectRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\ThirdPartyApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Show a list of tasks.
     */
    public function index(TaskIndexRequest $request): Response
    {
        $state = $request->tableState();

        $query = Task::query()
            ->select('tasks.*')
            ->forFocusedClient($request->user()->focusedClientId())
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
            ->leftJoin('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
            ->with(['project.client', 'taskStatus']);

        if ($state['q'] !== '') {
            $term = '%'.addcslashes($state['q'], '%_\\').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('tasks.name', 'like', $term)
                    ->orWhere('tasks.description', 'like', $term)
                    ->orWhere('projects.name', 'like', $term)
                    ->orWhere('clients.name', 'like', $term);
            });
        }

        if ($state['project_id'] !== null) {
            $query->where('tasks.project_id', $state['project_id']);
        }

        if ($state['status_id'] !== null) {
            $query->where('tasks.status_id', $state['status_id']);
        }

        match ($state['sort']) {
            'name'    => $query->orderBy('tasks.name', $state['direction']),
            'project' => $query
                ->orderBy('projects.name', $state['direction'])
                ->orderBy('tasks.id', 'desc'),
            'client' => $query
                ->orderBy('clients.name', $state['direction'])
                ->orderBy('tasks.id', 'desc'),
            'status' => $query
                ->orderBy('task_statuses.name', $state['direction'])
                ->orderBy('tasks.id', 'desc'),
            default => $query->orderBy('tasks.id', $state['direction']),
        };

        $tasks = $query
            ->paginate($state['per_page'])
            ->withQueryString();

        $statuses = $state['project_id'] !== null
            ? TaskStatus::query()
                ->where('project_id', $state['project_id'])
                ->orderBy('sort_order')
                ->get(['id', 'name'])
            : [];

        return Inertia::render('Task/Index', [
            'tasks'    => $tasks,
            'projects' => Project::notArchived()->orderBy('name')->get(['id', 'name']),
            'statuses' => $statuses,
            'table'    => [
                'filters' => [
                    'q'          => $state['q'],
                    'project_id' => $state['project_id'] !== null ? (string) $state['project_id'] : '',
                    'status_id'  => $state['status_id'] !== null ? (string) $state['status_id'] : '',
                ],
                'sort'      => $state['sort'],
                'direction' => $state['direction'],
                'per_page'  => $state['per_page'],
            ],
        ]);
    }

    public function bulkAssignProject(BulkAssignTasksToProjectRequest $request): RedirectResponse
    {
        $project = Project::query()->notArchived()->findOrFail($request->validated('project_id'));
        $tasks = Task::whereIn('id', $request->validated('task_ids'))->get();

        foreach ($tasks as $task) {
            $previousStatusId = $task->status_id;
            $statusId = $previousStatusId !== null
                && TaskStatus::query()->where('id', $previousStatusId)->where('project_id', $project->id)->exists()
                ? $previousStatusId
                : (TaskStatus::query()->where('project_id', $project->id)->where('is_default', true)->first()?->id
                    ?? TaskStatus::query()->where('project_id', $project->id)->orderBy('sort_order')->first()?->id);

            $task->update([
                'project_id' => $project->id,
                'status_id'  => $statusId,
            ]);
        }

        $count = $tasks->count();

        return redirect()
            ->route('task.index')
            ->with('success', sprintf(
                '%d %s assigned to "%s".',
                $count,
                $count === 1 ? 'task' : 'tasks',
                $project->name,
            ));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request): Response
    {
        $prefillProjectId = null;
        if ($request->filled('project_id')) {
            $id = (int) $request->query('project_id');
            if ($id > 0 && Project::query()->notArchived()->whereKey($id)->exists()) {
                $prefillProjectId = $id;
            }
        }

        return Inertia::render('Task/Create', [
            'projects'           => Project::notArchived()->with(['sprints', 'tasks', 'taskStatuses', 'jiraIntegration'])->orderBy('name')->get(),
            'tasks'              => Task::orderBy('id', 'desc')->get(),
            'prefill_project_id' => $prefillProjectId,
        ]);
    }

    /**
     * Store a new task in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'project_id'     => 'exists:projects,id',
            'status_id'      => 'nullable|exists:task_statuses,id',
            'jira_issue_key' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-z][A-Za-z0-9_]*-\d+$/',
                Rule::unique('tasks', 'jira_issue_key')->where(function ($query) {
                    return $query->where('project_id', request('project_id'));
                }),
            ],
            'name'           => [
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('project_id', request('project_id'));
                }),
            ],
        ]);

        $statusId = request('status_id');

        // If no status_id provided, try to get the default status for the project
        if (!$statusId && request('project_id')) {
            $defaultStatus = TaskStatus::where('project_id', request('project_id'))
                ->where('is_default', true)
                ->first();
            $statusId = $defaultStatus ? $defaultStatus->id : null;
        }

        $task = Task::create([
            'name'           => request('name'),
            'description'    => request('description') ?? '',
            'project_id'     => request('project_id') ?? null,
            'status_id'      => $statusId,
            'status'         => 'incomplete',
            'jira_issue_key' => request('jira_issue_key'),
        ]);

        return redirect()
            ->route('task.index')
            ->with('success', 'Created task "'.$task->name.'"');
    }

    /**
     * Show a single task.
     */
    public function show(Task $task): Response
    {
        return Inertia::render('Task/Show', [
            'task'                   => $task->load('project.client', 'project.taskStatuses', 'sessions.sessionCategory', 'taskStatus'),
            'totalDurationForHumans' => $task->sessions->totalDurationForHumans(),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    /**
     * Edit a task.
     */
    public function edit(Task $task): Response
    {
        // Store the intended URL in the session for redirect after update
        $intendedUrl = request()->header('referer', route('task.index'));
        session(['url.intended' => $intendedUrl]);

        return Inertia::render('Task/Edit', [
            'task'     => $task->load('taskStatus'),
            'projects' => Project::notArchived()->with(['taskStatuses', 'jiraIntegration'])->get(),
        ]);
    }

    /**
     * Update the task in the database.
     */
    public function update(Task $task): RedirectResponse
    {
        request()->validate([
            'name'           => 'string',
            'description'    => 'string',
            'project_id'     => 'exists:projects,id',
            'status_id'      => 'nullable|exists:task_statuses,id',
            'jira_issue_key' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-z][A-Za-z0-9_]*-\d+$/',
                Rule::unique('tasks', 'jira_issue_key')
                    ->where(fn ($query) => $query->where('project_id', request('project_id', $task->project_id)))
                    ->ignore($task->id),
            ],
        ]);

        $task->update([
            'name'           => request('name', $task->name),
            'description'    => request('description', $task->description),
            'project_id'     => request('project_id', $task->project_id),
            'status_id'      => request('status_id', $task->status_id),
            'jira_issue_key' => request()->has('jira_issue_key') ? request('jira_issue_key') : $task->jira_issue_key,
        ]);

        // Use Laravel's intended redirect with fallback
        return redirect()->intended(route('task.index'))
            ->with('success', 'Task "'.$task->name.'" updated successfully.');
    }

    /**
     * Update the task status (for kanban drag-and-drop).
     */
    public function updateStatus(Request $request, Task $task): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'status_id' => [
                'required',
                'exists:task_statuses,id',
                Rule::exists('task_statuses', 'id')->where(function ($query) use ($task) {
                    $query->where('project_id', $task->project_id);
                }),
            ],
        ]);

        $task->update([
            'status_id' => $request->input('status_id'),
        ]);

        return response()->json([
            'success' => true,
            'task'    => $task->fresh(['taskStatus']),
        ]);
    }

    /**
     * Delete a task from the database.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('task.index')
            ->with('success', 'Task "'.$task->name.'" deleted');
    }
}
