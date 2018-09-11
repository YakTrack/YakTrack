<?php

namespace App\Support;

class DateIntervalFormatter
{
    const FOR_HUMANS_FORMAT = '%H:%I:%S';

    public function forHumans($interval)
    {
        return $interval->format(self::FOR_HUMANS_FORMAT);
    }
}
