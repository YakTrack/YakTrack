<?php

namespace App\Statistics;

use App\Session;
use App\Support\DateIntervalFormatter;

class Sessions
{
    protected $dateIntervalFormatter;

    public function __construct(DateIntervalFormatter $dateIntervalFormatter)
    {
        $this->dateIntervalFormatter = $dateIntervalFormatter;
    }

    public function totalTimeOnDate($date)
    {
        return $this->dateIntervalFormatter->createFromSeconds(
            Session::onDate($date)->get()->totalDurationInSeconds()
        );
    }
}
