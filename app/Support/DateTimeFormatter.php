<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Collection;

class DateTimeFormatter
{
    const DATETIME_FOR_HUMANS_FORMAT = 'l d/m/Y g:i:s a';

    const DATETIME_FOR_MYSQL_FORMAT = 'Y-m-d H:i:s';

    const DATE_FOR_HUMANS_FORMAT = 'l jS M Y';

    const DATE_FOR_HUMANS_COMPACT_FORMAT = 'D jS M';

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

    public function dateTimeForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->format($dateTime, self::DATETIME_FOR_HUMANS_FORMAT);
    }

    public function localDateTimeForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->localFormat($dateTime, self::DATETIME_FOR_HUMANS_FORMAT);
    }

    public function dateForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->format($dateTime, self::DATE_FOR_HUMANS_FORMAT);
    }

    public function dateForHumansCompact(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->format($dateTime, self::DATE_FOR_HUMANS_COMPACT_FORMAT);
    }

    public function dateNoYearForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->format($dateTime, self::DATE_NO_YEAR_FOR_HUMANS_FORMAT);
    }

    public function localDateForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->localFormat($dateTime, self::DATE_FOR_HUMANS_FORMAT);
    }

    public function date(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->dateForHumans($dateTime);
    }

    public function localDate(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->localDateForHumans($dateTime);
    }

    public function timeForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->format($dateTime, self::TIME_FOR_HUMANS_FORMAT);
    }

    public function localTimeForHumans(DateTimeInterface|string|null $dateTime): ?string
    {
        return $this->localFormat($dateTime, self::TIME_FOR_HUMANS_FORMAT);
    }

    public function utcFormat(DateTimeInterface|string|null $dateTime, ?string $format = null): ?string
    {
        $dateTime = $dateTime instanceof Carbon ? clone $dateTime : new Carbon($dateTime, $this->timezone());

        return $this->format($this->toUTC($dateTime), $format);
    }

    public function format(DateTimeInterface|string|null $dateTime, ?string $format = null): ?string
    {
        if (is_null($format)) {
            $format = self::DATETIME_FOR_MYSQL_FORMAT;
        }

        try {
            $dateTime = Carbon::parse($dateTime);
            return $dateTime->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function localFormat(DateTimeInterface|null $dateTime, ?string $format = null): ?string
    {
        $dateTime = is_null($dateTime) ? Carbon::now() : Carbon::parse($dateTime);

        return $this->format($dateTime->timezone($this->timezone()), $format);
    }

    public function inputFormat(DateTimeInterface|string|null $dateTime): ?string
    {
        if (is_null($dateTime)) {
            return null;
        }

        $dateTime = $dateTime instanceof Carbon ? $dateTime : Carbon::parse($dateTime);
        $localDateTime = $dateTime->timezone($this->timezone());

        return $localDateTime->format('Y-m-d\TH:i');
    }

    public function timezone(): string
    {
        return config('app.display_timezone', config('app.timezone'));
    }

    public function today(?string $format = null): Carbon
    {
        return Carbon::now()->timezone($this->timezone())->hour(0)->minute(0)->second(0);
    }

    public function toUTC(Carbon $date): Carbon
    {
        return (clone $date)->timezone('UTC');
    }

    public function startOfWeek(?string $format = null): ?string
    {
        return $this->format(Carbon::now()->setTimezone($this->timezone())->startOfWeek()->timezone('UTC'), $format);
    }

    public function endOfWeek(?string $format = null): ?string
    {
        return $this->format(Carbon::now()->setTimeZone($this->timezone())->startOfweek()->addWeek()->setTimezone('UTC'), $format);
    }

    public function tomorrow(?string $format = null): ?string
    {
        return $this->format(Carbon::tomorrow()->setTimezone($this->timezone()), $format);
    }

    /**
     * @return Collection<int, string>
     */
    public function daysOfWeek(): Collection
    {
        return collect(self::DAYS_OF_WEEK);
    }

    public function dayThisWeek(string $day): Carbon
    {
        return $this->daysThisWeek()[$this->daysOfWeek()->search(ucfirst(strtolower($day)))];
    }

    /**
     * @return Collection<int, Carbon>
     */
    public function daysThisWeek(): Collection
    {
        $startOfWeek = Carbon::now()->timezone($this->timezone())->startOfWeek();

        return Collection::times(7, function ($day) use ($startOfWeek) {
            return $startOfWeek->copy()->addDays($day - 1);
        });
    }
}
