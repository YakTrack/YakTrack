<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\View\View;
use Inertia\Response;

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
    public function index(): Response
    {
        $clientUser = auth('client')->user();

        $projects = $clientUser->client->projects()
            ->notArchived()
            ->with(['tasks' => function ($query) {
                $query->with(['taskStatus', 'sessions' => function ($sessionQuery) {
                    $sessionQuery->whereBillable()
                        ->with('sessionCategory');
                }])
                ->orderBy('name');
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
    public function show(Project $project): Response
    {
        $clientUser = auth('client')->user();

        // Ensure the project belongs to the client
        if ($project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to project.');
        }

        // Ensure the project is not archived
        if ($project->isArchived()) {
            abort(404, 'Project not found.');
        }

        $project->load(['tasks' => function ($query) {
            $query->with(['taskStatus', 'sessions' => function ($sessionQuery) {
                $sessionQuery->whereBillable()
                    ->with('sessionCategory')
                    ->orderBy('started_at', 'desc');
            }])
            ->orderBy('name');
        }]);

        return inertia('ClientPortal/Projects/Show', [
            'clientUser' => $clientUser,
            'project'    => $project,
        ]);
    }

    /**
     * Download PDF report for the specified project.
     */
    public function downloadReport(Project $project): HttpResponse
    {
        $clientUser = auth('client')->user();

        // Ensure the project belongs to the client
        if ($project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to project.');
        }

        // Ensure the project is not archived
        if ($project->isArchived()) {
            abort(404, 'Project not found.');
        }

        $project->load(['tasks' => function ($query) {
            $query->with(['taskStatus', 'sessions' => function ($sessionQuery) {
                $sessionQuery->whereBillable()
                    ->with(['sessionCategory', 'invoice'])
                    ->orderBy('started_at', 'desc');
            }])
            ->orderBy('name');
        }]);

        $pdf = Pdf::loadView('client-portal.project-report', [
            'project'    => $project,
            'clientUser' => $clientUser,
        ]);

        return $pdf->download($project->name.' - Project Report.pdf');
    }

    /**
     * View HTML report for the specified project.
     */
    public function viewReport(Project $project): View
    {
        $clientUser = auth('client')->user();

        // Ensure the project belongs to the client
        if ($project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to project.');
        }

        // Ensure the project is not archived
        if ($project->isArchived()) {
            abort(404, 'Project not found.');
        }

        $project->load(['tasks' => function ($query) {
            $query->with(['taskStatus', 'sessions' => function ($sessionQuery) {
                $sessionQuery->whereBillable()
                    ->with(['sessionCategory', 'invoice'])
                    ->orderBy('started_at', 'desc');
            }])
            ->orderBy('name');
        }]);

        return view('client-portal.project-report-html', [
            'project'    => $project,
            'clientUser' => $clientUser,
        ]);
    }
}
