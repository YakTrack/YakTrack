<?php

namespace App\Statistics;

use App\Models\Session;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;

class Sessions
{
    protected $dateIntervalFormatter;
    protected $dateTimeFormatter;

    public function __construct(DateTimeFormatter $dateTimeFormatter, DateIntervalFormatter $dateIntervalFormatter)
    {
        $this->dateIntervalFormatter = $dateIntervalFormatter;
        $this->dateTimeFormatter     = $dateTimeFormatter;
    }

    public function totalTimeOnDate($date)
    {
        return $this->dateIntervalFormatter->createFromSeconds(
            Session::onDate($date)->get()->totalDurationInSeconds()
        );
    }

    public function thisWeeksWorkSessions()
    {
        return $this->dateTimeFormatter->daysThisWeek()->map(function ($date) {
            return [
                'date'            => $date,
                'dateForHumans'   => $this->dateTimeFormatter->dateForHumans($date),
                'totalTimeWorked' => $this->dateIntervalFormatter->forHumans($this->totalTimeOnDate($date)),
            ];
        });
    }
}
