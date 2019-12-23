<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Project/Index', [
            'projects' => Project::orderBy('name')->with('client')->get()
        ]);
    }

    /**
     * Show the form for creating a new project.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project.create', ['clients' => Client::all()]);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
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
            ->with(
                ['messages' => [
                    'success' => 'You have created a new project called '.
                    $project->name,
                ],
                ]
            );
    }

    /**
     * Show a single project
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
        return view('project.edit', [
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
            ->with(['messages' => ['success' => 'Project '.$project->name.' updated.']]);
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
            ->with(['messages' => ['success' => 'You have deleted Project ' . $project->name . '.']]);
    }
}
