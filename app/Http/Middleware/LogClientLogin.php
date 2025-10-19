<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogClientLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for successful client portal requests
        if ($request->is('client-portal*') && 
            $response->getStatusCode() === 200 && 
            auth('client')->check()) {
            
            $clientUser = auth('client')->user();
            
            // Check if this is a new session (no active session for this user)
            $hasActiveSession = $clientUser->activeLoginSessions()
                ->where('session_id', session()->getId())
                ->exists();
            
            if (!$hasActiveSession) {
                // Log the login session
                $clientUser->logLoginSession([
                    'login_method' => 'password', // Could be enhanced to detect other methods
                    'referrer' => $request->header('referer'),
                    'request_uri' => $request->getRequestUri(),
                ]);
            }
        }

        return $response;
    }
}
