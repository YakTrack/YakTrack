<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DateTimeFormatter
{
    const DATETIME_FOR_HUMANS_FORMAT = 'l d/m/Y g:i:s a';

    const DATETIME_FOR_MYSQL_FORMAT = 'Y-m-d H:i:s';

    const DATE_FOR_HUMANS_FORMAT = 'l jS M Y';

    const DATE_NO_YEAR_FOR_HUMANS_FORMAT = 'l jS M';

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

    public function dateNoYearForHumans($dateTime)
    {
        return $this->format($dateTime, self::DATE_NO_YEAR_FOR_HUMANS_FORMAT);
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
        $dateTime = $dateTime instanceof Carbon ? clone $dateTime : new Carbon($dateTime, $this->timezone());

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
        $dateTime = is_null($dateTime) ? Carbon::now() : $dateTime->copy();

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
        return (clone $date)->timezone('UTC');
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
        $startOfWeek = Carbon::now()->timezone($this->timezone())->startOfWeek();

        return Collection::times(7, function ($day) use ($startOfWeek) {
            return $startOfWeek->copy()->addDays($day - 1);
        });
    }
}
