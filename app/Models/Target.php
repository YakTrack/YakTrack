<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Target extends Model
{
    const DURATION_UNITS = [
        'DAYS' => [
            'key' => 'days',
        ],
        'HOURS' => [
            'key' => 'hours',
        ],
    ];

    const VALUE_UNITS = [
        'HOURS' => [
            'key' => 'hours',
        ]
    ];

    public static function findForDate($date)
    {
        return self::whereForDate($date)
            ->first();
    }

    public function scopeWhereForDate($query, $date)
    {
        return $query
            ->whereDurationUnit(self::DURATION_UNITS['DAYS']['key'])
            ->whereDuration(1)
            ->whereStartsAt(Carbon::parse($date)->toDateTimeString());
    }

    public function sessions()
    {
        return Session::whereStartedAfter($this->starts_at)
            ->whereFinishedBefore($this->endsAt());
    }

    public function endsAt()
    {
        return Carbon::parse($this->starts_at)->add($this->duration_unit, $this->duration);
    }

    public function hoursRemaining()
    {
        return $this->valueInHours() - $this->sessions()->get()->totalDurationInHours();
    }

    public function valueInHours()
    {
        return $this->value;
    }
}
