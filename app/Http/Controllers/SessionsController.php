<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\RedirectResponse;

class SessionsController extends Controller
{
    public function update(): RedirectResponse
    {
        $sessionData = request('sessions', []);

        request()->validate([
            'sessions' => 'required|array',
            'sessions.*' => 'required|array',
        ]);

        // Validate that all session IDs exist
        foreach (array_keys($sessionData) as $sessionId) {
            if (!Session::find($sessionId)) {
                abort(422, "Session {$sessionId} not found");
            }
        }

        $sessions = collect($sessionData)
            ->keys()
            ->map(function ($id) {
                return Session::find($id);
            })->each(function ($session) use ($sessionData) {
                $session->update($sessionData[$session->id]);
            });

        return redirect()->back();
    }
}
