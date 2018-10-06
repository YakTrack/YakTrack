<?php

namespace App\Support;

use Carbon\Carbon;

class DateTimeFormatter
{
    const DATETIME_FOR_HUMANS_FORMAT = 'l d/m/Y g:i:s a';

    const DATETIME_FOR_MYSQL_FORMAT = 'Y-m-d H:i:s';

    const DATE_FOR_HUMANS_FORMAT = 'l jS M Y';

    const TIME_FOR_HUMANS_FORMAT = 'g:i:s a';

    const DAYS_OF_WEEK = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function dateTimeForHumans($dateTime)
    {
        return $this->format($dateTime, self::DATETIME_FOR_HUMANS_FORMAT);
    }

    public function localDateTimeForHumans($dateTime)
    {
        return $this->localFormat($dateTime, self::DATETIME_FOR_HUMANS_FORMAT);
    }

    public function dateForHumans($dateTime)
    {
        return $this->format($dateTime, self::DATE_FOR_HUMANS_FORMAT);
    }

    public function localDateForHumans($dateTime)
    {
        return $this->localFormat($dateTime, self::DATE_FOR_HUMANS_FORMAT);
    }

    public function timeForHumans($dateTime)
    {
        return $this->format($dateTime, self::TIME_FOR_HUMANS_FORMAT);
    }

    public function localTimeForHumans($dateTime)
    {
        return $this->localFormat($dateTime, self::TIME_FOR_HUMANS_FORMAT);
    }

    public function utcFormat($dateTime, $format = null)
    {
        return $this->format($this->toUTC($dateTime), $format);
    }

    public function format($dateTime, $format = null)
    {
        if (is_null($format)) {
            $format = self::DATETIME_FOR_MYSQL_FORMAT;
        }

        $dateTime = Carbon::parse($dateTime);

        if (!$dateTime) {
            return;
        }

        return $dateTime->format($format);
    }

    public function localFormat($dateTime, $format = null)
    {
        if (is_null($dateTime)) {
            $dateTime = Carbon::now();
        }

        return $this->format($dateTime->timezone($this->timezone()), $format);
    }

    public function timezone()
    {
        return config('app.display_timezone', config('app.timezone'));
    }

    public function today($format = null)
    {
        return Carbon::now()->timezone($this->timezone())->hour(0)->minute(0)->second(0);
    }

    public function toUTC(Carbon $date)
    {
        return $date->timezone('UTC');
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
        return $this->daysOfWeek()->map(function ($dayOfWeek) {
            $day = Carbon::parse($dayOfWeek)->timezone($this->timezone())->hour(0);

            if ($day->lt(Carbon::parse('last monday')->timezone($this->timezone()))) {
                return $day->addWeek();
            }

            if ($day->gte(Carbon::parse('next monday')->timezone($this->timezone()))) {
                return $day->subWeek();
            }

            return $day;
        });
    }
}
