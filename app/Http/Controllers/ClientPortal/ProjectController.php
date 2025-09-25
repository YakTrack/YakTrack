<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Display a listing of the client's projects.
     */
    public function index()
    {
        $clientUser = auth('client')->user();

        $projects = $clientUser->client->projects()
            ->with(['tasks' => function ($query) {
                $query->with(['sessions' => function ($sessionQuery) {
                    $sessionQuery->whereBillable()
                        ->with('sessionCategory');
                }]);
            }])
            ->get();

        return inertia('ClientPortal/Projects/Index', [
            'clientUser' => $clientUser,
            'projects'   => $projects,
        ]);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $clientUser = auth('client')->user();

        // Ensure the project belongs to the client
        if ($project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to project.');
        }

        $project->load(['tasks' => function ($query) {
            $query->with(['sessions' => function ($sessionQuery) {
                $sessionQuery->whereBillable()
                    ->with('sessionCategory')
                    ->orderBy('started_at', 'desc');
            }]);
        }]);

        return inertia('ClientPortal/Projects/Show', [
            'clientUser' => $clientUser,
            'project'    => $project,
        ]);
    }
}
