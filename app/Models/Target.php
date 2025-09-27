<?php

namespace App\Models;

use App\Models\Collections\TargetCollection;
use App\Support\DateTimeFormatter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Target extends Model
{
    use HasFactory;

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
        ],
    ];

    public static function findForDate(DateTimeInterface|string $date): ?self
    {
        return self::whereForDate(Carbon::parse($date)->format('Y-m-d'))
            ->first();
    }

    /**
     * Create a new Eloquent Collection instance.
     * @param array<int, \App\Models\Target> $models
     */
    public function newCollection(array $models = []): TargetCollection
    {
        return new TargetCollection($models);
    }

    /**
     * @param Builder<\App\Models\Target> $query
     * @return Builder<\App\Models\Target>
     */
    public function scopeWhereBillableOnly(Builder $query): Builder
    {
        return $query->where('billable_only', 1);
    }

    /**
     * @param Builder<\App\Models\Target> $query
     * @return Builder<\App\Models\Target>
     */
    public function scopeWhereNotBillableOnly(Builder $query): Builder
    {
        return $query->where('billable_only', 0);
    }

    /**
     * @param Builder<\App\Models\Target> $query
     * @return Builder<\App\Models\Target>
     */
    public function scopeWhereForDate(Builder $query, ?string $date = null): Builder
    {
        $query->whereDurationUnit(self::DURATION_UNITS['DAYS']['key'])
            ->whereDuration(1);

        if ($date) {
            $query->whereStartsAt(Carbon::parse($date)->toDateTimeString());
        }

        return $query;
    }

    /**
     * @param Builder<\App\Models\Target> $query
     * @return Builder<\App\Models\Target>
     */
    public function scopeWhereForThisWeek(Builder $query): Builder
    {
        $query->whereForDate();

        return $query->whereIn('starts_at', app(DateTimeFormatter::class)->daysThisWeek()->map->toDateTimeString());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session>
     */
    public function sessions(): \Illuminate\Database\Eloquent\Collection
    {
        $sessionsQuery = Session::startedAfter(\Carbon\Carbon::parse($this->starts_at))
            ->startedBefore($this->endsAt());

        if ($this->billable_only) {
            $sessionsQuery->whereBillable();
        }

        return $sessionsQuery->get();
    }

    public function endsAt(): Carbon
    {
        return Carbon::parse($this->starts_at)->add($this->duration_unit, $this->duration);
    }

    public function secondsRemaining(): int
    {
        return $this->valueInSeconds() - $this->sessions()->totalDurationInSeconds();
    }

    public function valueInSeconds(): int
    {
        return (int) ($this->valueInHours() * 3600);
    }

    public function hoursRemaining(): float
    {
        return $this->valueInHours() - $this->sessions()->totalDurationInHours();
    }

    public function valueInHours(): float
    {
        return $this->value;
    }

    public function isThisWeek(): bool
    {
        $startOfWeek = app(DateTimeFormatter::class)->startOfWeek();
        $endOfWeek = app(DateTimeFormatter::class)->endOfWeek();
        
        return Carbon::parse($this->starts_at)->between($startOfWeek, $endOfWeek);
    }
}
