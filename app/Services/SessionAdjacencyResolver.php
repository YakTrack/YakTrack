<?php

namespace App\Services;

use App\Models\Session;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SessionAdjacencyResolver
{
    public function __construct(private DateTimeFormatter $dateTimeFormatter)
    {
    }

    /**
     * Resolve, for each session, whether a session may be added immediately
     * before or after it, along with the default times such a session would
     * take. A session may be added on a given side when no existing session
     * is touching it on that side.
     *
     * @param Collection<int, Session> $sessions
     *
     * @return array<int, array{can_add_session_before: bool, can_add_session_after: bool, add_before_started_at: string, add_before_ended_at: string, add_after_started_at: string|null, add_after_ended_at: string|null}>
     */
    public function resolve(Collection $sessions): array
    {
        $map = [];

        foreach ($sessions as $session) {
            $map[$session->id] = $this->resolveForSession($session, $sessions);
        }

        return $map;
    }

    /**
     * @param Collection<int, Session> $sessions
     *
     * @return array{can_add_session_before: bool, can_add_session_after: bool, add_before_started_at: string, add_before_ended_at: string, add_after_started_at: string|null, add_after_ended_at: string|null}
     */
    private function resolveForSession(Session $session, Collection $sessions): array
    {
        $others = $sessions->reject(fn (Session $other): bool => $other->id === $session->id);

        $hasSessionImmediatelyBefore = $others->contains(
            fn (Session $other): bool => $other->ended_at !== null && $other->ended_at->equalTo($session->started_at)
        );

        $hasSessionImmediatelyAfter = $session->ended_at !== null && $others->contains(
            fn (Session $other): bool => $other->started_at->equalTo($session->ended_at)
        );

        return [
            'can_add_session_before' => !$hasSessionImmediatelyBefore,
            'can_add_session_after'  => $session->ended_at !== null && !$hasSessionImmediatelyAfter,
            ...$this->beforeDefaults($session, $others),
            ...$this->afterDefaults($session, $others),
        ];
    }

    /**
     * @param Collection<int, Session> $others
     *
     * @return array{add_before_started_at: string, add_before_ended_at: string}
     */
    private function beforeDefaults(Session $session, Collection $others): array
    {
        /** @var Carbon|null $previousEnd */
        $previousEnd = $others
            ->filter(fn (Session $other): bool => $other->ended_at !== null && $other->ended_at->lessThanOrEqualTo($session->started_at))
            ->max(fn (Session $other): Carbon => $other->ended_at);

        $startedAt = $previousEnd ?? $session->started_at->copy()->subHour();

        return [
            'add_before_started_at' => $this->localDateTime($startedAt),
            'add_before_ended_at'   => $this->localDateTime($session->started_at),
        ];
    }

    /**
     * @param Collection<int, Session> $others
     *
     * @return array{add_after_started_at: string|null, add_after_ended_at: string|null}
     */
    private function afterDefaults(Session $session, Collection $others): array
    {
        if ($session->ended_at === null) {
            return [
                'add_after_started_at' => null,
                'add_after_ended_at'   => null,
            ];
        }

        /** @var Carbon|null $nextStart */
        $nextStart = $others
            ->filter(fn (Session $other): bool => $other->started_at->greaterThanOrEqualTo($session->ended_at))
            ->min(fn (Session $other): Carbon => $other->started_at);

        $endedAt = $nextStart ?? $session->ended_at->copy()->addHour();

        return [
            'add_after_started_at' => $this->localDateTime($session->ended_at),
            'add_after_ended_at'   => $this->localDateTime($endedAt),
        ];
    }

    private function localDateTime(Carbon $dateTime): string
    {
        return $this->dateTimeFormatter->localFormat($dateTime, DateTimeFormatter::DATETIME_FOR_MYSQL_FORMAT);
    }
}
