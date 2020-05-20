<?php

namespace App\Statistics;

use App\Models\Session;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use Illuminate\Support\Carbon;

class Sessions
{
    protected $dateIntervalFormatter;
    protected $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter, DateIntervalFormatter $dateIntervalFormatter)
    {
        $this->dateIntervalFormatter = $dateIntervalFormatter;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function totalTimeOnDate($date)
    {
        return $this->dateIntervalFormatter->createFromSeconds($this->totalSecondsOnDate($date));
    }

    public function totalSecondsOnDate($date)
    {
        return $this->sessionsOnDate($date)->totalDurationInSeconds();
    }

    public function thisWeeksWorkSessions()
    {
        return $this->dateTimeFormatter->daysThisWeek()->map(function ($date) {
            return [
                'date'                => $date,
                'dateForHumans'       => $this->dateTimeFormatter->dateForHumans($date),
                'dateNoYearForHumans' => $this->dateTimeFormatter->dateNoYearForHumans($date),
                'totalTimeWorked'     => $this->dateIntervalFormatter->forHumans($this->totalTimeOnDate($date)),
                'totalSecondsWorked'  => $this->totalSecondsOnDate($date),
                'currentlyWorking'    => !$this->currentSession()
                    ? false
                    : $this->sessionsOnDate($date)->pluck('id')->contains($this->currentSession()->id),
                'isToday'             => Carbon::parse($date)->isToday(),
            ];
        });
    }

    public function currentlyWorking()
    {
        return $this->currentSession() != null;
    }

    public function currentSession()
    {
        return Session::whereNull('ended_at')->first();
    }

    public function sessionsOnDate($date)
    {
        return Session::onDate($date)->get();
    }
}
