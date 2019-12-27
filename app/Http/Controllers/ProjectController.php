<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Show a list of projects.
     */
    public function index()
    {
        return Inertia::render('Project/Index', [
            'projects' => Project::orderBy('name')->with('client')->get(),
        ]);
    }

    /**
     * Show the form for creating a new project.
     **/
    public function create()
    {
        return Inertia::render('Project/Edit', [
            'clients' => Client::all(),
        ]);
    }

    /**
     * Save a new project.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'client_id' => 'exists:clients,id',
        ]);

        $project = factory(Project::class)->create($request->except('_token'));

        return redirect()
            ->route('project.index')
            ->with('success', "Project \"$project->name\" created");
    }

    /**
     * Show a single project.
     */
    public function show(Project $project)
    {
        return Inertia::render('Project/Show', [
            'project' => $project->load('client'),
        ]);
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param Project $project
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->validate($request, [
            'name'      => 'required',
            'client_id' => 'exists:clients,id',
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
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        if (!$project->isDeletable()) {
            abort(422, 'Project is unable to be deleted');
        }

        $project->delete();

        return redirect()
            ->route('project.index')
            ->with(['messages' => ['success' => 'You have deleted Project '.$project->name.'.']]);
    }
}
