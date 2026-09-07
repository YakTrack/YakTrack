<?php

namespace App\Models;

use App\Models\Collections\SessionCollection;
use App\Support\DateIntervalFormatter;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\Session> whereThisWeek()
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\Session> whereBillable()
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\Session> whereNotBillable()
 * @method static \Illuminate\Database\Eloquent\Builder<\App\Models\Session> whereOnDayThisWeek(string $day)
 *
 * @property-read bool $isRunning
 */
class Session extends Model
{
    use Concerns\CanBeBillable;
    /** @use HasFactory<\Database\Factories\SessionFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @var array<string>
     */
    protected $dates = ['started_at', 'ended_at'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_billable'             => 'boolean',
        'pending_tasks_seeded_at' => 'datetime',
    ];

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
        'startedAtInputFormat',
        'endedAtInputFormat',
    ];

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return BelongsTo<Sprint, $this>
     */
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<SessionCategory, $this>
     */
    public function sessionCategory(): BelongsTo
    {
        return $this->belongsTo(SessionCategory::class);
    }

    /**
     * @return HasMany<ThirdPartyApplicationSession, $this>
     */
    public function thirdPartyApplicationSessions(): HasMany
    {
        return $this->hasMany(ThirdPartyApplicationSession::class);
    }

    /**
     * @return HasMany<SessionPendingTask, $this>
     */
    public function pendingTasks(): HasMany
    {
        return $this->hasMany(SessionPendingTask::class);
    }

    public function getStartedAtAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['started_at']);
    }

    public function getLocalStartedAtAttribute(): Carbon
    {
        return $this->startedAt->timezone(app(DateTimeFormatter::class)->timezone());
    }

    public function getEndedAtAttribute(): ?Carbon
    {
        if (!isset($this->attributes['ended_at'])) {
            return null;
        }

        return Carbon::parse($this->attributes['ended_at']);
    }

    public function getLocalEndedAtAttribute(): ?Carbon
    {
        if (is_null($this->endedAt)) {
            return null;
        }

        return $this->endedAt->timezone(app(DateTimeFormatter::class)->timezone());
    }

    public function getStartedAtDateAttribute(): string
    {
        return app(DateTimeFormatter::class)->date($this->startedAt);
    }

    public function getLocalStartedAtDateAttribute(): string
    {
        return app(DateTimeFormatter::class)->localDate($this->startedAt);
    }

    public function getLocalStartedAtDateForHumansAttribute(): string
    {
        return app(DateTimeFormatter::class)->localDateForHumans($this->startedAt);
    }

    public function getEndedAtDateAttribute(): ?string
    {
        return app(DateTimeFormatter::class)->date($this->endedAt);
    }

    public function getLocalEndedAtDateAttribute(): ?string
    {
        return app(DateTimeFormatter::class)->localDate($this->endedAt);
    }

    public function getLocalEndedAtDateForHumansAttribute(): ?string
    {
        return app(DateTimeFormatter::class)->localDateForHumans($this->endedAt);
    }

    public function getDurationForHumansAttribute(): string
    {
        return app(DateIntervalFormatter::class)->forHumans($this->duration);
    }

    public function getDurationAttribute(): \DateInterval
    {
        return ($this->ended_at ?? Carbon::now())->diff(Carbon::parse($this->started_at));
    }

    public function getDurationInSecondsAttribute(): int
    {
        return $this->duration->days * 86400 + $this->duration->h * 3600 + $this->duration->i * 60 + $this->duration->s;
    }

    public function getDurationInHoursAttribute(): float
    {
        return $this->durationInSeconds / 3600;
    }

    public function getEndedAtTimeForHumansAttribute(): ?string
    {
        return (new DateTimeFormatter())->timeForHumans($this->ended_at);
    }

    public function getLocalEndedAtTimeForHumansAttribute(): ?string
    {
        return (new DateTimeFormatter())->localTimeForHumans($this->ended_at);
    }

    public function getStartedAtTimeForHumansAttribute(): string
    {
        return (new DateTimeFormatter())->timeForHumans($this->started_at);
    }

    public function getLocalStartedAtTimeForHumansAttribute(): string
    {
        return (new DateTimeFormatter())->localTimeForHumans($this->started_at);
    }

    public function getEndedAtDateTimeForHumansAttribute(): ?string
    {
        return (new DateTimeFormatter())->dateTimeForHumans($this->ended_at);
    }

    public function getLocalEndedAtDateTimeForHumansAttribute(): ?string
    {
        return (new DateTimeFormatter())->localDateTimeForHumans($this->ended_at);
    }

    public function getStartedAtDateTimeForHumansAttribute(): string
    {
        return (new DateTimeFormatter())->dateTimeForHumans($this->started_at);
    }

    public function isRunning(): bool
    {
        return is_null($this->ended_at);
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->isRunning();
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereIsRunning(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }

    /**
     * @deprecated in favour of scopeWhereIsRunning
     *
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $this->scopeWhereIsRunning($query);
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeToday(Builder $query): Builder
    {
        return $this->scopeWhereOnDate($query, app(DateTimeFormatter::class)->today());
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereOnDate(Builder $query, DateTimeInterface $date): Builder
    {
        $carbonDate = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        return $query->where('started_at', '>=', (new DateTimeFormatter())->utcFormat($date))
            ->where('started_at', '<', (new DateTimeFormatter())->utcFormat($carbonDate->addDays(1)));
    }

    /**
     * @deprecated in favour of whereOnDate
     *
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeOnDate(Builder $query, DateTimeInterface $date): Builder
    {
        return $this->scopeWhereOnDate($query, $date);
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereThisWeek(Builder $query): Builder
    {
        return $query->where('started_at', '>=', (new DateTimeFormatter())->startOfWeek())
            ->where('started_at', '<', (new DateTimeFormatter())->endOfWeek());
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereOnDayThisWeek(Builder $query, string $day): Builder
    {
        $startOfDay = app(DateTimeFormatter::class)->dayThisWeek($day)->startOfDay()->utc()->toDateTimeString();
        $endOfDay = app(DateTimeFormatter::class)->dayThisWeek($day)->endOfDay()->utc()->toDateTimeString();

        return $query->where('started_at', '>=', $startOfDay)
            ->where('started_at', '<', $endOfDay);
    }

    /**
     * Deprecated in favour of whereThisWeek.
     *
     * @deprecated
     *
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $this->scopeWhereThisWeek($query);
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * Scope to the focused client via task -> project. No-op when no client is focused.
     *
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeForFocusedClient(Builder $query, ?int $clientId): Builder
    {
        return $query->when($clientId, fn (Builder $query): Builder => $query->whereHas('task.project', fn (Builder $project): Builder => $project->where('client_id', $clientId)));
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeStartedAfter(Builder $query, DateTimeInterface $date): Builder
    {
        return $query->where('started_at', '>=', $date);
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeStartedBefore(Builder $query, DateTimeInterface $date): Builder
    {
        return $query->where('started_at', '<=', $date);
    }

    public function stop(?DateTimeInterface $endedAt = null): void
    {
        $this->ended_at = $endedAt ? Carbon::parse($endedAt) : Carbon::now();
        $this->save();
    }

    public function getStopUrlAttribute(): string
    {
        return route('session.stop');
    }

    public function getStartedAtInputFormatAttribute(): ?string
    {
        if (!$this->attributes['started_at']) {
            return null;
        }

        return $this->localStartedAt->format('Y-m-d\TH:i');
    }

    public function getEndedAtInputFormatAttribute(): ?string
    {
        if (!$this->ended_at) {
            return null;
        }

        return $this->localEndedAt->format('Y-m-d\TH:i');
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array<int, \App\Models\Session> $models
     *
     * @return \App\Models\Collections\SessionCollection
     *
     * @phpstan-return \App\Models\Collections\SessionCollection
     */
    public function newCollection(array $models = []): SessionCollection
    {
        return new SessionCollection($models);
    }

    public function exportToThirdPartyApplication(ThirdPartyApplication $thirdPartyApplication): ThirdPartyApplicationSession
    {
        return ThirdPartyApplicationSession::create([
            'session_id'                 => $this->id,
            'third_party_application_id' => $thirdPartyApplication->id,
        ]);
    }

    /**
     * @param Builder<\App\Models\Session> $sessions
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeLinkedTo(Builder $sessions, ThirdPartyApplication $app): Builder
    {
        return $sessions->whereHas('thirdPartyApplicationSessions', function ($thirdPartyApplicationSession) use ($app) {
            return $thirdPartyApplicationSession->whereThirdPartyApplicationId($app->id);
        });
    }

    public function isLinkedTo(ThirdPartyApplication $app): bool
    {
        return $this->thirdPartyApplicationSessions()
            ->get()
            ->filter(function ($thirdPartyApplicationSession) use ($app) {
                return $thirdPartyApplicationSession->isForThirdPartyApplication($app);
            })->count() > 0;
    }

    public function linkTo(ThirdPartyApplication $app): ThirdPartyApplicationSession
    {
        return ThirdPartyApplicationSession::create([
            'session_id'                 => $this->id,
            'third_party_application_id' => $app->id,
        ]);
    }

    public function isThisWeek(): bool
    {
        return $this->startedAt >= (new DateTimeFormatter())->startOfWeek()
            && $this->startedAt < (new DateTimeFormatter())->endOfWeek();
    }

    public function attachToInvoice(Invoice $invoice): bool
    {
        return $this->update([
            'invoice_id' => $invoice->id,
        ]);
    }

    public function hasNoClient(): bool
    {
        return $this->getClient() === null;
    }

    public function getClient(): ?Client
    {
        if ($this->task === null) {
            return null;
        }

        if ($this->task->project === null) {
            return null;
        }

        return $this->task->project->client;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
