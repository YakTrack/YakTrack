<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Inertia\Response;

class SessionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Display a listing of the client's billable sessions.
     */
    public function index(): Response
    {
        $clientUser = auth('client')->user();

        $sessions = Session::whereHas('task.project', function ($query) use ($clientUser) {
            $query->where('client_id', $clientUser->client_id);
        })
        ->whereBillable()
        ->with(['task.project', 'sessionCategory'])
        ->orderBy('started_at', 'desc')
        ->paginate(20);

        return inertia('ClientPortal/Sessions/Index', [
            'clientUser' => $clientUser,
            'sessions'   => $sessions,
        ]);
    }

    /**
     * Display the specified session.
     */
    public function show(Session $session): Response
    {
        $clientUser = auth('client')->user();

        // Ensure the session belongs to the client
        if ($session->task->project->client_id !== $clientUser->client_id) {
            abort(403, 'Unauthorized access to session.');
        }

        $session->load(['task.project', 'sessionCategory']);

        return inertia('ClientPortal/Sessions/Show', [
            'clientUser' => $clientUser,
            'session'    => $session,
        ]);
    }
}
