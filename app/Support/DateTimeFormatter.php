<?php

namespace App\Support;

use Carbon\Carbon;

class DateTimeFormatter
{
    const DATETIME_FOR_HUMANS_FORMAT = 'l d/m/Y g:i:s a';

    const DATETIME_FOR_MYSQL_FORMAT = 'Y-m-d H:i:s';

    const TIME_FOR_HUMANS_FORMAT = 'g:i:s a';

    const DAYS_OF_WEEK = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ];

    public function dateTimeForHumans($dateTime, $applyTimeZone = true)
    {
        return $this->format($dateTime, self::DATETIME_FOR_HUMANS_FORMAT, $applyTimeZone);
    }

    public function timeForHumans($dateTime, $applyTimeZone = true)
    {
        return $this->format($dateTime, self::TIME_FOR_HUMANS_FORMAT, $applyTimeZone);
    }

    public function format($dateTime, $format = null, $applyTimezone = false)
    {
        if (is_null($format)) {
            $format = self::DATETIME_FOR_MYSQL_FORMAT;
        }

        $dateTime = Carbon::parse($dateTime);

        if (!$dateTime) {
            return null;
        }

        if ($applyTimezone) {
            $dateTime->timezone($this->timezone());
        }

        return $dateTime->format($format);
    }

    public function timezone()
    {
        return config('app.display_timezone', config('app.timezone'));
    }

    public function today($format = null)
    {
        return $this->format(Carbon::today()->setTimezone($this->timezone()), $format);
    }

    public function startOfWeek($format = null)
    {
        return $this->format(Carbon::now()->setTimezone($this->timezone())->startOfWeek()->timezone('UTC'), $format);
    }

    public function endOfWeek($format = null)
    {
        return $this->format(Carbon::now()->setTimeZone($this->timezone())->startOfweek()->addWeek()->setTimezone('UTC'), $format);
    }

    public function tomorrow($format = null)
    {
        return $this->format(Carbon::tomorrow()->setTimezone($this->timezone()), $format);
    }

    public function daysOfWeek()
    {
        return collect(self::DAYS_OF_WEEK);
    }

    public function daysThisWeek()
    {
        return $this->daysOfWeek()->map(function ($day) {
            if (Carbon::today()->format('l') == $day) {
                return Carbon::today()->setTimezone($this->timezone());
            }

            return Carbon::parse('last '.$day)->setTimezone($this->timezone());
        })->keyBy(function ($day) {
            return $day->format('l');
        })->filter(
            function ($day, $key) {
                return $day->startOfDay()->timestamp >= Carbon::parse('last monday')->setTimezone($this->timezone())->startOfDay()->timestamp;
            }
        );
    }
}
