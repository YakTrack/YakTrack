<?php

namespace App\Statistics;

use App\Models\Session;
use App\Models\Target;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use DateInterval;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Sessions
{
    protected DateIntervalFormatter $dateIntervalFormatter;
    protected DateTimeFormatter $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter, DateIntervalFormatter $dateIntervalFormatter)
    {
        $this->dateIntervalFormatter = $dateIntervalFormatter;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function totalTimeOnDate(\Carbon\Carbon $date): DateInterval
    {
        return $this->dateIntervalFormatter->createFromSeconds($this->totalSecondsOnDate($date));
    }

    public function totalSecondsOnDate(\Carbon\Carbon $date): int
    {
        return $this->sessionsOnDate($date)->totalDurationInSeconds();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{date: \Carbon\Carbon, totalSecondsWorked: int, totalSecondsTarget: int, currentlyWorking: bool, isToday: bool}>
     */
    public function thisWeeksWorkSessions(): \Illuminate\Support\Collection
    {
        return $this->dateTimeFormatter->daysThisWeek()->map(function ($date) {
            $target = Target::findForDate($date->format('Y-m-d'));

            return [
                'date'                          => $date,
                'totalSecondsWorked'            => $this->totalSecondsOnDate($date),
                'totalSecondsTarget'            => $target ? $target->valueInSeconds() : 0,
                'currentlyWorking'              => !$this->currentSession()
                    ? false
                    : $this->sessionsOnDate($date)->pluck('id')->contains($this->currentSession()->id),
                'isToday'                       => Carbon::parse($date)->isToday(),
            ];
        });
    }

    public function currentlyWorking(): bool
    {
        return $this->currentSession() != null;
    }

    public function currentSession(): ?Session
    {
        return Session::whereNull('ended_at')->first();
    }

    /**
     * @return Collection<int, Session>
     */
    public function sessionsOnDate(\Carbon\Carbon $date): Collection
    {
        return Session::onDate($date)->get();
    }
}
