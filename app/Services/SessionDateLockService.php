<?php

namespace App\Services;

use App\Models\LockedSessionDate;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Collection;

class SessionDateLockService
{
    public function isDateLocked(User $user, string $date): bool
    {
        return LockedSessionDate::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->exists();
    }

    public function isSessionLocked(Session $session, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user === null) {
            return false;
        }

        return $this->isDateLocked($user, $session->localStartedAt->format('Y-m-d'));
    }

    public function toggle(User $user, string $date): bool
    {
        $existing = LockedSessionDate::query()
            ->where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return false;
        }

        LockedSessionDate::create([
            'user_id' => $user->id,
            'date'    => $date,
        ]);

        return true;
    }

    /**
     * @return Collection<int, string>
     */
    public function lockedDatesForUser(User $user): Collection
    {
        return LockedSessionDate::query()
            ->where('user_id', $user->id)
            ->pluck('date')
            ->map(fn ($date) => $date->format('Y-m-d'));
    }

    /**
     * @param  list<string>  $dates
     */
    public function lockDates(User $user, array $dates): int
    {
        $locked = 0;

        foreach ($dates as $date) {
            if ($this->isDateLocked($user, $date)) {
                continue;
            }

            LockedSessionDate::create([
                'user_id' => $user->id,
                'date'    => $date,
            ]);

            $locked++;
        }

        return $locked;
    }

    /**
     * @param  list<string>  $dates
     */
    public function unlockDates(User $user, array $dates): int
    {
        return LockedSessionDate::query()
            ->where('user_id', $user->id)
            ->whereIn('date', $dates)
            ->delete();
    }

    public function ensureSessionCanBeEdited(Session $session): void
    {
        if ($this->isSessionLocked($session)) {
            abort(403, 'This session date is locked for editing.');
        }
    }
}
