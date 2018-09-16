<?php

namespace App\Support;

use DateInterval;
use DateTime;

class DateIntervalFormatter
{
    const FOR_HUMANS_FORMAT = ':%I:%S';

    public function forHumans(DateInterval $interval)
    {
        return $interval->format($this->numberOfHours($interval).self::FOR_HUMANS_FORMAT);
    }

    public function createFromSeconds($seconds)
    {
        $now = new DateTime;
        $afterNumberOfSeconds = new DateTime;
        $afterNumberOfSeconds->add(new DateInterval('PT'.$seconds.'S'));

        return $afterNumberOfSeconds->diff($now);
    }

    protected function numberOfHours(DateInterval $interval)
    {
        return $interval->days * 24 + $interval->h;
    }
}
