<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ThirdPartyApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Show a list of tasks
     */
    public function index()
    {
        return Inertia::render('Task/Index', [
            'tasks' => Task::orderBy('id', 'desc')
                ->with('project.client', 'parent')
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new task
     */
    public function create()
    {
        return Inertia::render('Task/Edit', [
            'projects' => Project::with(['sprints', 'tasks'])->orderBy('name')->get(),
            'tasks'    => Task::orderBy('id', 'desc')->get(),
        ]);
    }

    /**
     * Store a new task in the database
     */
    public function store(Request $request)
    {
        $task = Task::create([
            'name'        => request('name'),
            'description' => request('description') ?? '',
            'project_id'  => request('project_id') ?? null,
            'parent_id'   => request('parent_id') ?? null,
            'status'      => 'incomplete',
        ]);

        return redirect()
            ->route('task.index')
            ->with('success', 'Created task "' . $task->name . '"');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return view('task.show', [
            'task'                   => $task->load('sessions'),
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        return view('task.edit', [
            'task'     => $task,
            'projects' => Project::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $task->update(request()->all());

        return redirect()->route('task.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index');
    }
}
