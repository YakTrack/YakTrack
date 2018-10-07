<?php

namespace App\Models;

use App\Models\Collections\SessionCollection;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    // protected $dates = ['started_at', 'ended_at'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function thirdPartyApplicationSessions()
    {
        return $this->hasMany(ThirdPartyApplicationSession::class);
    }

    public function getStartedAtAttribute()
    {
        return Carbon::parse($this->attributes['started_at']);
    }

    public function getLocalStartedAtAttribute()
    {
        return $this->startedAt->timezone(app(DateTimeFormatter::class)->timezone());
    }

    public function getEndedAtAttribute()
    {
        if (is_null($this->attributes['ended_at'])) {
            return;
        }

        return Carbon::parse($this->attributes['ended_at']);
    }

    public function getLocalEndedAtAttribute()
    {
        return $this->endedAt->timezone(app(DateTimeFormatter::class)->timezone());
    }

    public function getStartedAtDateAttribute()
    {
        return app(DateTimeFormatter::class)->date($this->startedAt);
    }

    public function getLocalStartedAtDateAttribute()
    {
        return app(DateTimeFormatter::class)->localDate($this->startedAt);
    }

    public function getLocalStartedAtDateForHumansAttribute()
    {
        return app(DateTimeFormatter::class)->localDateForHumans($this->startedAt);
    }

    public function getEndedAtDateAttribute()
    {
        return app(DateTimeFormatter::class)->date($this->endedAt);
    }

    public function getLocalEndedAtDateAttribute()
    {
        return app(DateTimeFormatter::class)->localDate($this->endedAt);
    }

    public function getLocalEndedAtDateForHumansAttribute()
    {
        return app(DateTimeFormatter::class)->localDateForHumans($this->endedAt);
    }

    public function getDurationForHumansAttribute()
    {
        return app(DateIntervalFormatter::class)->forHumans($this->duration);
    }

    public function getDurationAttribute()
    {
        return ($this->ended_at ?? Carbon::now())->diff(Carbon::parse($this->started_at));
    }

    public function getDurationInSecondsAttribute()
    {
        return $this->duration->days * 86400 + $this->duration->h * 3600 + $this->duration->i * 60 + $this->duration->s;
    }

    public function getEndedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->timeForHumans($this->ended_at);
    }

    public function getLocalEndedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->localTimeForHumans($this->ended_at);
    }

    public function getStartedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->timeForHumans($this->started_at);
    }

    public function getLocalStartedAtTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->localTimeForHumans($this->started_at);
    }

    public function getEndedAtDateTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->dateTimeForHumans($this->ended_at);
    }

    public function getLocalEndedAtDateTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->localDateTimeForHumans($this->ended_at);
    }

    public function getStartedAtDateTimeForHumansAttribute()
    {
        return (new DateTimeFormatter())->dateTimeForHumans($this->started_at);
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
        return $query->onDate(app(DateTimeFormatter::class)->today());
    }

    public function scopeOnDate($query, $date)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter())->utcFormat($date))
            ->where('started_at', '<', (new DateTimeFormatter())->utcFormat($date->addDays(1)));
    }

    public function scopeThisWeek($query)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter())->startOfWeek())
            ->where('started_at', '<', (new DateTimeFormatter())->endOfWeek());
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
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new SessionCollection($models);
    }

    public function exportToThirdPartyApplication(ThirdPartyApplication $thirdPartyApplication)
    {
        return ThirdPartyApplicationSession::create([
            'session_id'                 => $this->id,
            'third_party_application_id' => $thirdPartyApplication->id,
        ]);
    }

    public function isLinkedTo(ThirdPartyApplication $app)
    {
        return $this->thirdPartyApplicationSessions()
            ->get()
            ->filter(function ($thirdPartyApplicationSession) use ($app) {
                return $thirdPartyApplicationSession->isForThirdPartyApplication($app);
            })->count() > 0;
    }
}
