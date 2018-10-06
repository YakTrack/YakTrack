<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    /**
     * Display a listing of the sprints.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sprint.index', ['sprints' => Sprint::all()]);
    }

    /**
     * Show the form for creating a new sprint.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sprint.create', ['projects' => Project::all()]);
    }

    /**
     * Store a newly created sprint in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required|unique:sprints,name',
            'project_id' => 'exists:projects,id',
        ]);

        $sprint = Sprint::create($request->all());

        return redirect()
            ->route('sprint.index')
            ->with(['messages' => ['success' => 'You have added sprint '.$sprint->name.'.']]);
    }

    /**
     * Display the specified sprint.
     *
     * @param Sprint $sprint
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Sprint $sprint)
    {
        return view('sprint.show', ['sprint' => $sprint]);
    }

    /**
     * Show the form for editing the specified sprint.
     *
     * @param Sprint $sprint
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Sprint $sprint)
    {
        return view('sprint.edit', [
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sprint $sprint)
    {
        $this->validate($request, [
            'name'       => 'required|unique:sprints,name,'.$sprint->id,
            'project_id' => 'exists:projects,id',
        ]);

        $sprint->update($request->all());

        return redirect()
            ->route('sprint.index')
            ->with(['messages' => ['success' => 'You have updated sprint '.$sprint->name.'.']]);
    }

    /**
     * Remove the specified sprint from storage.
     *
     * @param Sprint $sprint
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sprint $sprint)
    {
        $sprint->delete();

        return redirect()
            ->route('sprint.index')
            ->with(['messages' => ['success' => 'You have deleted sprint '.$sprint->name.'.']]);
    }
}
