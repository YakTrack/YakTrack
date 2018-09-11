<?php

namespace App;

use App\Support\DateTimeFormatter;
use App\Models\Collections\SessionCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    // protected $dates = ['started_at', 'ended_at'];

    public function getStartedAtAttribute()
    {
        return Carbon::parse($this->attributes['started_at']);
    }

    public function getEndedAtAttribute()
    {
        if (is_null($this->attributes['ended_at'])) {
            return null;
        }

        return Carbon::parse($this->attributes['ended_at']);
    }

    public function getTotalTimeAttribute()
    {
        if (!$this->duration) {
            return null;
        }

        return $this->duration->format('%H:%I:%S');
    }

    public function getDurationAttribute()
    {
        if (!$this->ended_at) {
            return null;
        }

        return Carbon::parse($this->ended_at)->diff(Carbon::parse($this->started_at));
    }

    public function getDurationInSecondsAttribute()
    {
        return $this->duration->days * 86400 + $this->duration->h * 3600 + $this->duration->i * 60 + $this->duration->s;
    }

    public function getEndedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter)->timeForHumans($this->ended_at);
    }

    public function getStartedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter)->timeForHumans($this->started_at);
    }

    public function getEndedAtDateTimeForHumansAttribute()
    {
        return (new DateTimeFormatter)->dateTimeForHumans($this->ended_at);
    }

    public function getStartedAtDateTimeForHumansAttribute()
    {
        return (new DateTimeFormatter)->dateTimeForHumans($this->started_at);
    }

    public function isRunning()
    {
        return is_null($this->ended_at);
    }

    public function scopeRunning($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeToday($query)
    {
        return $query->onDate(Carbon::today());
    }

    public function scopeOnDate($query, $date)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter)->format($date))
            ->where('started_at', '<', (new DateTimeFormatter)->format($date->addDays(1)));
    }

    public function scopeThisWeek($query)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter)->startOfWeek())
            ->where('started_at', '<', (new DateTimeFormatter)->endOfWeek());
    }

    public function scopeFinished($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function stop($endedAt = null)
    {
        $this->ended_at = $endedAt ?? Carbon::now();
        $this->save();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new SessionCollection($models);
    }
}
