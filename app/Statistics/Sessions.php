<?php

namespace App\Statistics;

use App\Session;
use DateInterval;

class Sessions
{
    public function totalTimeOnDate($date)
    {
        return DateInterval::createFromDateString(
            Session::onDate($date)->get()->totalDurationInSeconds().' seconds'
        );
    }
}
