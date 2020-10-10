<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Support\DateTimeFormatter;
use App\Models\Collections\TargetCollection;

class Target extends Model
{
    protected $guarded = [];

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
        return self::whereForDate(Carbon::parse($date)->format('Y-m-d'))
            ->first();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new TargetCollection($models);
    }

    public function scopeWhereForDate($query, $date = null)
    {
        $query->whereDurationUnit(self::DURATION_UNITS['DAYS']['key'])
            ->whereDuration(1);
        
        if ($date) {
            $query->whereStartsAt(Carbon::parse($date)->toDateTimeString());
        }
    }

    public function scopeWhereForThisWeek($query)
    {
        $query->whereForDate();

        $query->whereIn('starts_at', app(DateTimeFormatter::class)->daysThisWeek()->map->toDateTimeString());
    }

    public function sessions()
    {
        $sessionsQuery = Session::startedAfter($this->starts_at)
            ->startedBefore($this->endsAt());
    
        if ($this->billable_only) {
            $sessionsQuery->whereBillable();
        }

        return $sessionsQuery->get();
    }

    public function endsAt()
    {
        return Carbon::parse($this->starts_at)->add($this->duration_unit, $this->duration);
    }

    public function secondsRemaining()
    {
        return $this->valueInSeconds() - $this->sessions()->totalDurationInSeconds();
    }

    public function valueInSeconds()
    {
        return $this->valueInHours() * 3600;
    }

    public function hoursRemaining()
    {
        return $this->valueInHours() - $this->sessions()->totalDurationInHours();
    }

    public function valueInHours()
    {
        return $this->value;
    }
}
