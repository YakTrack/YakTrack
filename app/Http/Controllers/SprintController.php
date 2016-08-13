<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Sprint;

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
        //
    }

    /**
     * Store a newly created sprint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified sprint.
     *
     * @param   Sprint $sprint
     * @return \Illuminate\Http\Response
     */
    public function show(Sprint $sprint)
    {
        //
    }

    /**
     * Show the form for editing the specified sprint.
     *
     * @param   Sprint $sprint
     * @return \Illuminate\Http\Response
     */
    public function edit(Sprint $sprint)
    {
        //
    }

    /**
     * Update the specified sprint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   Sprint $sprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sprint $sprint)
    {
        //
    }

    /**
     * Remove the specified sprint from storage.
     *
     * @param   Sprint $sprint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sprint $sprint)
    {
        //
    }
}
