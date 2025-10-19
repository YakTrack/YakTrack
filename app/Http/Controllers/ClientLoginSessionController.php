<?php

namespace App\Http\Controllers;

use App\Models\ClientLoginSession;
use App\Models\ClientUser;
use Illuminate\Http\Request;

class ClientLoginSessionController extends Controller
{
    /**
     * Display a listing of login sessions.
     */
    public function index(Request $request)
    {
        $query = ClientLoginSession::with('clientUser.client')
            ->latest('logged_in_at');

        // Filter by client user if specified
        if ($request->filled('client_user_id')) {
            $query->where('client_user_id', $request->client_user_id);
        }

        // Filter by active sessions
        if ($request->filled('active_only')) {
            $query->where('is_active', true);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('logged_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('logged_in_at', '<=', $request->date_to);
        }

        $sessions = $query->paginate(50);

        $clientUsers = ClientUser::with('client')->orderBy('name')->get();

        return inertia('ClientLoginSessions/Index', [
            'sessions' => $sessions,
            'clientUsers' => $clientUsers,
            'filters' => $request->only(['client_user_id', 'active_only', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Display login sessions for a specific client user.
     */
    public function forClientUser(ClientUser $clientUser)
    {
        $sessions = $clientUser->loginSessions()
            ->latest('logged_in_at')
            ->paginate(20);

        return inertia('ClientLoginSessions/ForClientUser', [
            'clientUser' => $clientUser->load('client'),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Display the specified login session.
     */
    public function show(ClientLoginSession $session)
    {
        $session->load('clientUser.client');

        return inertia('ClientLoginSessions/Show', [
            'session' => $session,
        ]);
    }

    /**
     * Log out a specific session.
     */
    public function logout(ClientLoginSession $session)
    {
        if ($session->is_active) {
            $session->markAsLoggedOut();
            
            return redirect()->back()
                ->with('success', 'Session has been logged out successfully.');
        }

        return redirect()->back()
            ->with('error', 'Session is already logged out.');
    }

    /**
     * Log out all active sessions for a client user.
     */
    public function logoutAllForUser(ClientUser $clientUser)
    {
        $activeCount = $clientUser->activeLoginSessions()->count();
        
        if ($activeCount > 0) {
            $clientUser->logOutAllSessions();
            
            return redirect()->back()
                ->with('success', "All {$activeCount} active sessions have been logged out.");
        }

        return redirect()->back()
            ->with('info', 'No active sessions found for this user.');
    }

    /**
     * Get login session statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_sessions' => ClientLoginSession::count(),
            'active_sessions' => ClientLoginSession::active()->count(),
            'sessions_today' => ClientLoginSession::whereDate('logged_in_at', today())->count(),
            'sessions_this_week' => ClientLoginSession::where('logged_in_at', '>=', now()->startOfWeek())->count(),
            'sessions_this_month' => ClientLoginSession::where('logged_in_at', '>=', now()->startOfMonth())->count(),
            'unique_users_today' => ClientLoginSession::whereDate('logged_in_at', today())
                ->distinct('client_user_id')
                ->count('client_user_id'),
            'unique_users_this_week' => ClientLoginSession::where('logged_in_at', '>=', now()->startOfWeek())
                ->distinct('client_user_id')
                ->count('client_user_id'),
            'unique_users_this_month' => ClientLoginSession::where('logged_in_at', '>=', now()->startOfMonth())
                ->distinct('client_user_id')
                ->count('client_user_id'),
        ];

        // Top active users
        $topActiveUsers = ClientUser::with('client')
            ->withCount(['loginSessions as recent_sessions_count' => function ($query) {
                $query->where('logged_in_at', '>=', now()->subDays(7));
            }])
            ->orderBy('recent_sessions_count', 'desc')
            ->limit(10)
            ->get();

        return inertia('ClientLoginSessions/Statistics', [
            'stats' => $stats,
            'topActiveUsers' => $topActiveUsers,
        ]);
    }
}
