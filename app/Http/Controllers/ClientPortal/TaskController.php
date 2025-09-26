<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Display a listing of the client's tasks.
     */
    public function index()
    {
        $clientUser = auth('client')->user();

        $tasks = Task::whereHas('project', function ($query) use ($clientUser) {
            $query->where('client_id', $clientUser->client_id);
        })
        ->with(['project', 'taskStatus', 'sessions' => function ($query) {
            $query->whereBillable()
                ->with('sessionCategory')
                ->orderBy('started_at', 'desc');
        }])
        ->orderBy('name')
        ->get();

        return inertia('ClientPortal/Tasks/Index', [
            'clientUser' => $clientUser,
            'tasks'      => $tasks,
        ]);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $clientUser = auth('client')->user();

        // Ensure the task belongs to the client
        if ($task->project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to task.');
        }

        $task->load(['project', 'taskStatus', 'sessions' => function ($query) {
            $query->whereBillable()
                ->with('sessionCategory')
                ->orderBy('started_at', 'desc');
        }]);

        return inertia('ClientPortal/Tasks/Show', [
            'clientUser' => $clientUser,
            'task'       => $task,
        ]);
    }
}
