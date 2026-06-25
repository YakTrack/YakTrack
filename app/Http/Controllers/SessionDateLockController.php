<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkSessionDateLockRequest;
use App\Services\SessionDateLockService;
use Illuminate\Http\RedirectResponse;

class SessionDateLockController extends Controller
{
    public function toggle(string $date, SessionDateLockService $sessionDateLockService): RedirectResponse
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            abort(404);
        }

        $isLocked = $sessionDateLockService->toggle(auth()->user(), $date);

        return redirect()
            ->back()
            ->with(
                'success',
                $isLocked
                    ? "Sessions for {$date} are now locked."
                    : "Sessions for {$date} are now unlocked."
            );
    }

    public function lockMany(BulkSessionDateLockRequest $request, SessionDateLockService $sessionDateLockService): RedirectResponse
    {
        /** @var list<string> $dates */
        $dates = $request->validated('dates');

        $locked = $sessionDateLockService->lockDates(auth()->user(), $dates);

        $message = match (true) {
            $locked === 0 => 'All selected dates are already locked.',
            $locked === 1  => '1 date locked.',
            default        => "{$locked} dates locked.",
        };

        return redirect()->back()->with('success', $message);
    }

    public function unlockMany(BulkSessionDateLockRequest $request, SessionDateLockService $sessionDateLockService): RedirectResponse
    {
        /** @var list<string> $dates */
        $dates = $request->validated('dates');

        $unlocked = $sessionDateLockService->unlockDates(auth()->user(), $dates);

        $message = match (true) {
            $unlocked === 0 => 'No selected dates were locked.',
            $unlocked === 1  => '1 date unlocked.',
            default          => "{$unlocked} dates unlocked.",
        };

        return redirect()->back()->with('success', $message);
    }
}
