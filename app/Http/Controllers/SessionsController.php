<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyManySessionsRequest;
use App\Models\Session;
use App\Services\SessionDateLockService;
use Illuminate\Http\RedirectResponse;

class SessionsController extends Controller
{
    public function __construct(private SessionDateLockService $sessionDateLockService) {}

    public function update(): RedirectResponse
    {
        $sessionData = request('sessions', []);

        request()->validate([
            'sessions'   => 'required|array',
            'sessions.*' => 'required|array',
        ]);

        // Validate that all session IDs exist
        foreach (array_keys($sessionData) as $sessionId) {
            if (!Session::find($sessionId)) {
                abort(422, "Session {$sessionId} not found");
            }
        }

        /** @var \Illuminate\Support\Collection<string, mixed> $sessionData */
        $sessions = collect($sessionData)
            ->keys()
            ->map(function (string $id) {
                return Session::find($id);
            });

        foreach ($sessions as $session) {
            $this->sessionDateLockService->ensureSessionCanBeEdited($session);
        }

        $sessions->each(function ($session) use ($sessionData) {
            $session->update($sessionData[$session->id]);
        });

        return redirect()->back();
    }

    public function destroyMany(DestroyManySessionsRequest $request): RedirectResponse
    {
        /** @var array<int, int> $sessionIds */
        $sessionIds = $request->validated('session_ids');

        $sessions = Session::query()->whereIn('id', $sessionIds)->get();

        foreach ($sessions as $session) {
            $this->sessionDateLockService->ensureSessionCanBeEdited($session);
        }

        $deleted = Session::query()->whereIn('id', $sessionIds)->delete();

        $message = $deleted === 1
            ? '1 session deleted.'
            : "{$deleted} sessions deleted.";

        return redirect()->back()->with('success', $message);
    }
}
