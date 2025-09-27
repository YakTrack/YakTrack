<?php

namespace App\Http\Controllers\ClientPortal;

use App\Http\Controllers\Controller;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Show the client portal dashboard.
     */
    public function index(): Response
    {
        $clientUser = auth('client')->user();

        $projects = $clientUser->client->projects()
            ->with(['tasks' => function ($query) {
                $query->with(['taskStatus', 'sessions' => function ($sessionQuery) {
                    $sessionQuery->whereBillable()
                        ->with('sessionCategory');
                }])
                ->orderBy('name');
            }])
            ->get();

        $billableSessions = $clientUser->billableSessions()->get();
        $totalBillableHours = $billableSessions->sum('duration_in_seconds') / 3600;

        $recentSessions = $clientUser->billableSessions()
            ->with(['task.project', 'sessionCategory'])
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        return inertia('ClientPortal/Dashboard', [
            'clientUser'         => $clientUser,
            'projects'           => $projects,
            'totalBillableHours' => round($totalBillableHours, 2),
            'recentSessions'     => $recentSessions,
        ]);
    }
}
