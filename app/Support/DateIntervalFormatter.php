<?php

namespace App\Support;

use DateInterval;
use DateTime;

class DateIntervalFormatter
{
    const FOR_HUMANS_FORMAT = '%H:%I:%S';

    public function forHumans($interval)
    {
        return $interval->format(self::FOR_HUMANS_FORMAT);
    }

    public function createFromSeconds($seconds)
    {
        $now = new DateTime;
        $afterNumberOfSeconds = new DateTime;
        $afterNumberOfSeconds->add(new DateInterval('PT'.$seconds.'S'));

        return $afterNumberOfSeconds->diff($now);
    }
}
