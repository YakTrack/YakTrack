<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\ThirdPartyApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Show a list of tasks.
     */
    public function index()
    {
        return Inertia::render('Task/Index', [
            'tasks' => Task::orderBy('id', 'desc')
                ->with('project.client', 'parent', 'taskStatus')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return Inertia::render('Task/Edit', [
            'projects' => Project::with(['sprints', 'tasks', 'taskStatuses'])->orderBy('name')->get(),
            'tasks'    => Task::orderBy('id', 'desc')->get(),
        ]);
    }

    /**
     * Store a new task in the database.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'project_id' => 'exists:projects,id',
            'status_id'  => 'nullable|exists:task_statuses,id',
            'name'       => [
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
            'name'        => request('name'),
            'description' => request('description') ?? '',
            'project_id'  => request('project_id') ?? null,
            'parent_id'   => request('parent_id') ?? null,
            'status_id'   => $statusId,
            'status'      => 'incomplete',
        ]);

        return redirect()
            ->route('task.index')
            ->with('success', 'Created task "'.$task->name.'"');
    }

    /**
     * Show a single task.
     */
    public function show(Task $task)
    {
        return Inertia::render('Task/Show', [
            'task'                   => $task->load('project.client', 'sessions.sessionCategory'),
            'totalDurationForHumans' => $task->sessions->totalDurationForHumans(),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    /**
     * Edit a task.
     */
    public function edit(Task $task)
    {
        return Inertia::render('Task/Edit', [
            'task'     => $task->load('taskStatus'),
            'tasks'    => Task::all(),
            'projects' => Project::with('taskStatuses')->get(),
        ]);
    }

    /**
     * Update the task in the database.
     */
    public function update(Task $task)
    {
        request()->validate([
            'name'        => 'string',
            'description' => 'string',
            'project_id'  => 'exists:projects,id',
            'parent_id'   => 'nullable|exists:tasks,id|not_in:'.$task->id,
            'status_id'   => 'nullable|exists:task_statuses,id',
        ]);

        $task->update([
            'name'        => request('name', $task->name),
            'description' => request('description', $task->description),
            'project_id'  => request('project_id', $task->project_id),
            'parent_id'   => request()->filled('parent_id') ? request('parent_id', $task->parent_id) : null,
            'status_id'   => request('status_id', $task->status_id),
        ]);

        return redirect()->route('task.index');
    }

    /**
     * Delete a task from the database.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('task.index')
            ->with('success', 'Task "'.$task->name.'" deleted');
    }
}
