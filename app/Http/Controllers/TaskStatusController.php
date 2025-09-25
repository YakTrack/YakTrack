<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of task statuses.
     */
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $project = null;
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $statuses = TaskStatus::where('project_id', $projectId)
                ->withCount('tasks')
                ->orderBy('sort_order')
                ->get();
        } else {
            $statuses = TaskStatus::with('project')
                ->withCount('tasks')
                ->orderBy('project_id')
                ->orderBy('sort_order')
                ->get();
        }
        
        return Inertia::render('TaskStatus/Index', [
            'taskStatuses' => $statuses,
            'project' => $project,
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new task status.
     */
    public function create(Request $request)
    {
        $projectId = $request->get('project_id');
        $project = $projectId ? Project::findOrFail($projectId) : null;
        
        return Inertia::render('TaskStatus/Edit', [
            'projects' => Project::orderBy('name')->get(),
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created task status.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_default' => 'boolean',
            'is_completed' => 'boolean',
        ]);

        $maxSortOrder = TaskStatus::where('project_id', $request->project_id)->max('sort_order') ?? -1;
        
        // Handle empty sort_order
        $sortOrder = $request->sort_order;
        if ($sortOrder === '' || $sortOrder === null) {
            $sortOrder = $maxSortOrder + 1;
        }
        
        $taskStatus = TaskStatus::create([
            'name' => $request->name,
            'project_id' => $request->project_id,
            'color' => $request->color ?? '#6B7280',
            'sort_order' => $sortOrder,
            'is_default' => $request->is_default ?? false,
            'is_completed' => $request->is_completed ?? false,
        ]);

        if ($request->is_default) {
            TaskStatus::where('project_id', $request->project_id)
                ->where('id', '!=', $taskStatus->id)
                ->update(['is_default' => false]);
        }

        return redirect()
            ->route('task-status.index', ['project_id' => $taskStatus->project_id])
            ->with('success', "Task status '{$taskStatus->name}' created");
    }

    /**
     * Show the form for editing the specified task status.
     */
    public function edit(TaskStatus $taskStatus)
    {
        return Inertia::render('TaskStatus/Edit', [
            'taskStatus' => $taskStatus->load('project'),
            'projects' => Project::orderBy('name')->get(),
            'project' => $taskStatus->project,
        ]);
    }

    /**
     * Update the specified task status.
     */
    public function update(Request $request, TaskStatus $taskStatus)
    {
        $request->validate([
            'name' => 'string|max:255',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_default' => 'boolean',
            'is_completed' => 'boolean',
        ]);

        $data = $request->only([
            'name', 'color', 'is_default', 'is_completed'
        ]);
        
        // Handle empty sort_order
        if ($request->has('sort_order')) {
            $sortOrder = $request->sort_order;
            if ($sortOrder === '' || $sortOrder === null) {
                $data['sort_order'] = null;
            } else {
                $data['sort_order'] = $sortOrder;
            }
        }

        $taskStatus->update($data);

        if ($request->is_default && $request->is_default === true) {
            TaskStatus::where('project_id', $taskStatus->project_id)
                ->where('id', '!=', $taskStatus->id)
                ->update(['is_default' => false]);
        }

        return redirect()
            ->route('task-status.index', ['project_id' => $taskStatus->project_id])
            ->with('success', "Task status '{$taskStatus->name}' updated");
    }

    /**
     * Remove the specified task status.
     */
    public function destroy(TaskStatus $taskStatus)
    {
        if ($taskStatus->tasks()->count() > 0) {
            return redirect()
                ->route('task-status.index', ['project_id' => $taskStatus->project_id])
                ->with('error', 'Cannot delete status that is assigned to tasks');
        }

        $taskStatusName = $taskStatus->name;
        $projectId = $taskStatus->project_id;
        $taskStatus->delete();

        return redirect()
            ->route('task-status.index', ['project_id' => $projectId])
            ->with('success', "Task status '{$taskStatusName}' deleted");
    }
}
