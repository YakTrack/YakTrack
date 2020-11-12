<?php

namespace App\Models;

use App\Models\Collections\SessionCollection;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;

class Session extends Model
{
    use Concerns\CanBeBillable;

    protected $guarded = [];

    protected $dates = ['started_at', 'ended_at'];

    protected $appends = [
        'durationForHumans',
        'durationInSeconds',
        'duration',
        'isRunning',
        'durationInSeconds',
        'localEndedAt',
        'localEndedAtTimeForHumans',
        'localStartedAt',
        'localStartedAtDateForHumans',
        'localStartedAtTimeForHumans',
        'stopUrl',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

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
        if (!isset($this->attributes['ended_at']) || is_null($this->attributes['ended_at'])) {
            return;
        }

        return Carbon::parse($this->attributes['ended_at']);
    }

    public function getLocalEndedAtAttribute()
    {
        if (is_null($this->endedAt)) {
            return;
        }

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

    public function getDurationInHoursAttribute()
    {
        return $this->durationInSeconds / 3600;
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

    public function getIsRunningAttribute()
    {
        return $this->isRunning();
    }

    public function scopeWhereIsRunning($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * @deprecated in favour of scopeWhereIsRunning
     */
    public function scopeRunning($query)
    {
        return $this->scopeWhereIsRunning($query);
    }

    public function scopeToday($query)
    {
        return $query->onDate(app(DateTimeFormatter::class)->today());
    }

    public function scopeWhereOnDate($query, $date)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter())->utcFormat($date))
            ->where('started_at', '<', (new DateTimeFormatter())->utcFormat((clone $date)->addDays(1)));
    }

    /**
     * @deprecated in favour of whereOnDate
     */
    public function scopeOnDate($query, $date)
    {
        return $this->scopeWhereOnDate($query, $date);
    }

    public function scopeWhereThisWeek($query)
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter())->startOfWeek())
            ->where('started_at', '<', (new DateTimeFormatter())->endOfWeek());
    }

    public function scopeWhereOnDayThisWeek($query, $day)
    {
        $startOfDay = app(DateTimeFormatter::class)->dayThisWeek($day)->startOfDay()->utc()->toDateTimeString();
        $endOfDay = app(DateTimeFormatter::class)->dayThisWeek($day)->endOfDay()->utc()->toDateTimeString();

        return $query->where('started_at', '>=', $startOfDay)
            ->where('started_at', '<', $endOfDay);
    }

    /**
     * Deprecated in favour of whereThisWeek
     * 
     * @deprecated
     */
    public function scopeThisWeek($query)
    {
        return $this->scopeWhereThisWeek($query);
    }

    public function scopeFinished($query)
    {
        return $query->whereNotNull('ended_at');
    }

    public function scopeStartedAfter($query, $date)
    {
        return $query->where('started_at', '>=', $date);
    }

    public function scopeStartedBefore($query, $date)
    {
        return $query->where('started_at', '<=', $date);
    }

    public function stop($endedAt = null)
    {
        $this->ended_at = $endedAt ?? Carbon::now();
        $this->save();
    }

    public function getStopUrlAttribute()
    {
        return route('session.stop');
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

    public function scopeLinkedTo($sessions, ThirdPartyApplication $app)
    {
        return $sessions->whereHas('thirdPartyApplicationSessions', function ($thirdPartyApplicationSession) use ($app) {
            return $thirdPartyApplicationSession->whereThirdPartyApplicationId($app->id);
        });
    }

    public function isLinkedTo(ThirdPartyApplication $app)
    {
        return $this->thirdPartyApplicationSessions()
            ->get()
            ->filter(function ($thirdPartyApplicationSession) use ($app) {
                return $thirdPartyApplicationSession->isForThirdPartyApplication($app);
            })->count() > 0;
    }

    public function linkTo(ThirdPartyApplication $app)
    {
        return ThirdPartyApplicationSession::create([
            'session_id'                 => $this->id,
            'third_party_application_id' => $app->id,
        ]);
    }

    public function isThisWeek()
    {
        return $this->startedAt >= (new DateTimeFormatter())->startOfWeek()
            && $this->startedAt < (new DateTimeFormatter())->endOfWeek();
    }

    public function attachToInvoice(Invoice $invoice)
    {
        return $this->update([
            'invoice_id' => $invoice->id,
        ]);
    }

    public function hasNoClient()
    {
        return $this->getClient() === null;
    }

    public function getClient()
    {
        if ($this->task === null) {
            return;
        }

        if ($this->task->project === null) {
            return;
        }

        return $this->task->project->client;
    }
}
