<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SprintController extends Controller
{
    /**
     * Display a list of sprints.
     */
    public function index(): Response
    {
        return Inertia::render('Sprint/Index', [
            'sprints' => Sprint::orderBy('id', 'desc')
                ->with('project', 'sessions')
                ->get()
                ->map(function ($sprint) {
                    $sprint->totalDurationForHumans = $sprint->sessions->totalDurationForHumans();
                    unset($sprint->sessions);

                    return $sprint;
                }),
        ]);
    }

    /**
     * Show the form for creating a new sprint.
     */
    public function create(): Response
    {
        return Inertia::render('Sprint/Edit', ['projects' => Project::all()]);
    }

    /**
     * Save a new sprint to the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name'       => 'required|unique:sprints,name',
            'project_id' => 'required|exists:projects,id',
            'is_open'    => 'boolean',
        ]);

        $sprint = Sprint::create($request->only([
            'name',
            'project_id',
            'is_open',
        ]));

        return redirect()
            ->route('sprint.index')
            ->with('success', 'Sprint "'.$sprint->name.'" created');
    }

    /**
     * Display a single sprint.
     */
    public function show(Sprint $sprint): Response
    {
        return Inertia::render('Sprint/Show', [
            'sprint' => $sprint->load('project', 'sessions.task.project'),
            'tasks'  => $sprint->sessions->groupBy('task_id')->map(function ($sessionsForTask) {
                $task = $sessionsForTask->first()->task;

                $task->totalDurationInSprintForHumans = $sessionsForTask->totalDurationForHumans();

                return $task;
            }),
            'totalDurationForHumans' => $sprint->sessions->totalDurationForHumans(),
        ]);
    }

    /**
     * Show the form for editing the specified sprint.
     */
    public function edit(Sprint $sprint): Response
    {
        return Inertia::render('Sprint/Edit', [
            'projects' => Project::all(),
            'sprint'   => $sprint,
        ]);
    }

    /**
     * Update the specified sprint in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Sprint                   $sprint
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sprint $sprint): RedirectResponse
    {
        $this->validate($request, [
            'name'       => 'required|unique:sprints,name,'.$sprint->id,
            'project_id' => 'required|exists:projects,id',
        ]);

        $sprint->update([
            'name'       => $request->name,
            'project_id' => $request->project_id,
            'is_open'    => $request->is_open == 'is_open' ? 1 : 0,
        ]);

        return redirect()
            ->route('sprint.index')
            ->with('success', 'Sprint "'.$sprint->name.'" updated');
    }

    /**
     * Remove the specified sprint from storage.
     *
     * @param Sprint $sprint
     */
    public function destroy(Sprint $sprint): RedirectResponse
    {
        $sprint->delete();

        return redirect()
            ->route('sprint.index')
            ->with('success', '"Sprint '.$sprint->name.'" deleted');
    }
}
